<?php
session_start();
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    private $data = array();

    public function __construct() {
        parent::__construct();
        $this->load->model(array('login_model','Emergency_model', 'webservice_model', 'Rider_registration_model','Rider_insurance_model','Apply_subscribe_model','Subscribe_details_model'));
        //	date_default_timezone_set('asia/calcutta');
    }

    function index() {
        $this->load->view('index');
    }

    function callbackurl() {
        $this->load->view('callback');
    }

    function verifyChecksum() {
        $this->load->view('verifychecksum');
    }

    function successurl() {
        $this->load->view('successpage');
    }

    function failurl() {
        $this->load->view('failpage');
    }

    function login_process() {
        $username = $this->input->post('username', TRUE);
        $password = md5($this->input->post('userpwd', TRUE));
        if (($login = $this->login_model->userlogin($username, $password)) != false) {
            $adminuser = $login->admin_name;
            $total_emergency = $this->login_model->get_emergency_count();
            $booking_count=$this->login_model->get_booking_count();
            $this->session->set_userdata('admin_name', $adminuser);
            $this->session->set_userdata('admin_id', $login->admin_id);
            $this->session->set_userdata('emergency',($total_emergency!=null) ? $total_emergency : "0");
            $this->session->set_userdata('booking_count',$booking_count);
            $this->session->set_flashdata('success', 'Welcome to Dashboard!!!');
            redirect('admin-monitoring');
        } else {
            $this->session->set_flashdata('error', 'Please enter valid Username or password');
            redirect('login');
        }
    }

    function dashboard() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['riders'] = $this->login_model->get_rider_count();
            $data['rider_online'] = $this->login_model->get_rider_online();
            $data['kilometer'] = $this->login_model->get_rider_kms();
            $data['customers'] = $this->login_model->get_user_count();
            $data['customer_online'] = $this->login_model->get_user_online();
            $data['total_amount'] = $this->login_model->total_amount();
            $data['results'] = $this->login_model->user_booking_today();
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
                    $result->intervaltaken=$interval->format('%H:%I:%S');
                }
            }
            $data['active'] = 1; //Navbar section: Active!!!
            $this->load->view('inc/header', $data);
            $this->load->view('dashboard/index', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function view_transaction() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id != null) {
            $per_page = $this->input->post('per_page');
            $search = $this->input->post('search');
            $per_page = (!isset($per_page)) ? 10 : $per_page;
            $getCount = $this->login_model->get_ride_list();
            $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

            $fromdatewise = $this->input->post('report_fromdate', TRUE);
            $todatewise = $this->input->post('report_todate', TRUE);
            $category = $this->input->post('report_type', TRUE);
            $this->session->set_flashdata('fromdatewise', $fromdatewise);
            $this->session->set_flashdata('todatewise', $todatewise);
            $this->session->set_flashdata('category', $category);
            if ($fromdatewise == "") {
                $fdate = $todatewise;
            } else {
                $fdate = $fromdatewise;
            }
            if ($todatewise == "") {
                $tdate = $fromdatewise;
            } else {
                $tdate = $todatewise;
            }
                $results = $this->login_model->get_ride_perPage($per_page, $page, $fdate, $tdate, $category);
                foreach ($results as $result) {
                    $result->user_details = "";
                    $result->rider_details = "";
                    $result->user_details = $this->login_model->get_user_name($result->book_userid);
                    $result->rider_details = $this->login_model->get_rider_all($result->allocate_riderid,$result->receive_riderid,$result->book_decline_id);
                }
            // }

            // $data['per_page'] = $per_page; //For view Page.!!!
            $data['results'] = (isset($results)) ? $results : "";
            $data['active'] = 2;
            $data['fromdatewise']=($fromdatewise!=null) ? $fromdatewise : "";
            $data['todatewise']=($todatewise!=null) ? $todatewise : "";
            $this->load->view('inc/header', $data);
            $this->load->view('transaction/index', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function report_wise() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 2;
            $fromdatewise = $this->input->post('report_fromdate', TRUE);
            $todatewise = $this->input->post('report_todate', TRUE);
            $category = $this->input->post('report_type', TRUE);

            $this->session->set_flashdata('fromdatewise', $fromdatewise);
            $this->session->set_flashdata('todatewise', $todatewise);
            $this->session->set_flashdata('category', $category);
            if ($fromdatewise == "") {
                $fdate = $todatewise;
            } else {
                $fdate = $fromdatewise;
            }
            if ($todatewise == "") {
                $tdate = $fromdatewise;
            } else {
                $tdate = $todatewise;
            }
            if (( $login = $this->login_model->get_report_details($fdate, $tdate, $category)) != false) {
                $results = $this->login_model->get_report_details($fdate, $tdate, $category);
                if ($results) {
                    foreach ($results as $result) {
                        $result->user_details = "";
                        $result->rider_details = "";
                        $result->user_details = $this->login_model->get_user_name($result->book_userid);
                        $result->rider_details = $this->login_model->get_rider_name($result->allocate_riderid);
                    }
                }
                $data['results'] = $results;
                $this->load->view('inc/page/header', $data);
                $this->load->view('transaction/index', $data);
                $this->load->view('inc/page/footer');
            } else {
                $results = $this->login_model->get_report_details($fdate, $tdate, $category);
                if ($results) {
                    foreach ($results as $result) {
                        $result->user_details = "";
                        $result->rider_details = "";
                        $result->user_details = $this->login_model->get_user_name($result->book_userid);
                        $result->rider_details = $this->login_model->get_rider_name($result->allocate_riderid);
                    }
                }
                $data['results'] = $results;
                $this->load->view('inc/header', $data);
                $this->load->view('transaction/index', $data);
                $this->load->view('inc/footer');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function mapview() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $this->load->library('googlemaps');
            $ids = $this->uri->segment(3);
            $ress = $this->login_model->update_retailerdetails($ids);
            $rideid = $ress->allocate_riderid;

            $mapview1 = $ress->book_fromlatitude . "," . $ress->book_fromlongitude;
            $mapview2 = $ress->book_tolatitude . "," . $ress->book_tolongitude;

            $res = $this->login_model->update_retailerdetailsrider($rideid);
            $mapview = $res->rider_latitude . "," . $res->rider_longitude;
            //$config['center'] = '13.0141, 80.2036';
            $config['zoom'] = 'auto';
            $config['directions'] = TRUE;
            $config['directionsStart'] = $mapview1;
            $marker = array();
            $marker['position'] = $mapview;
            $marker['icon'] = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=A|00FF00|000';
            $this->googlemaps->add_marker($marker);
            $config['directionsEnd'] = $mapview2;
            $config['directionsDivID'] = 'directionsDiv';
            $this->googlemaps->initialize($config);
            $data['map'] = $this->googlemaps->create_map();
            $data['result'] = $this->login_model->update_retailerdetails($ids);
            $data['active']=2;
            $this->load->view('inc/header', $data);
            $this->load->view('map/index', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function rider_list() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['results'] = $this->login_model->get_rider_list();
            $data['active'] = 4;
            $this->load->view('inc/header', $data);
            $this->load->view('rider/index', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function rider_document() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['results'] = $this->login_model->get_rider_list();
            $data['active'] = 6;
            $this->load->view('inc/header', $data);
            $this->load->view('riderdocument/index', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function admin_acceptrider() {
        $id = $this->uri->segment(3);
        $data['results'] = $this->login_model->accept_admin_rider($id);
        $ids = $this->login_model->accept_admin_ridersms($id);
        if ($ids->rider_adminaccept == 1) {
           $dlr_url = "";
           $type = "xml";
           $time = '';
           $unicode = '';
           $to = $ids->rider_mobileno;
           $message = "Hi%20" . $ids->rider_name . "%20your%20registration%20has%20been%20approved%20by%20PiggyBack.%20login%20and%20subscribe%20to%20take%20rides.";

           $url = "http://hpsms.dial4sms.com/api/web2sms.php?workingkey=Aee31e1af01a8d305496d795179a5a8a0&sender=HTHBKR&to=$to&message=$message&type=$type&dlr_url=$dlr_url$time$unicode";
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           $output = curl_exec($ch);
           if (curl_errno($ch)) {
               echo 'error:' . curl_error($ch);
           }
           curl_close($ch);
        }
        $this->session->set_flashdata('success','Rider Account is Activated');
        redirect('login/rider_document');
    }

    function admin_deactiverider() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
            $data['results'] = $this->login_model->deactive_admin_rider($id);
            $ids = $this->login_model->accept_admin_ridersms($id);
            if ($ids->rider_adminaccept == 0) {
               $dlr_url = "";
               $type = "xml";
               $time = '';
               $unicode = '';
               $to = $ids->rider_mobileno;
               $message = "Hi%20" . $ids->rider_name . "%20your%20account%20has%20been%20deactivated.%20Please%20contact%20admin%20to%20take%20rides.";

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
            }
            $this->push_deactivateUser($ids);
            $this->session->set_flashdata('error','Rider Account is Deactivated');
            redirect('login/rider_document');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function ride_list() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['results'] = $this->login_model->get_ride_list();
            $this->load->view('ridelist', $data);
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function add_coupon() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 7;
            $this->load->view('inc/header', $data);
            $this->load->view('coupon/add');
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function insert_coupons() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
            $data['active'] = 7;
            if ($id == "") {
                $itemid = explode("/", $this->input->post('coupon_edate', TRUE));
                $edate = (count($itemid) > 1) ? $itemid[2] . "-" . $itemid[1] . "-" . $itemid[0] : "";
                $insercoupon = array(
                    'coupon_title' => $this->input->post('coupon_title', TRUE),
                    'coupon_code' => $this->input->post('coupon_code', TRUE),
                    'coupon_offername' => $this->input->post('coupon_discount', TRUE),
                    'coupon_rtype' => $this->input->post('coupon_offer', TRUE),
                    'coupon_desc' => $this->input->post('coupon_description', TRUE),
                    'coupon_type' => $this->input->post('coupon_type', TRUE),
                    'coupon_date' => date('Y-m-d'),
                    'coupon_time' => date('H:i:s'),
                    'coupon_edate' => $this->input->post('coupon_edate', TRUE),
                    'coupon_applyuser' => $this->input->post('coupon_user', TRUE)
                );
                if (( $login = $this->login_model->insert_coupondetails($insercoupon)) != false) {
                    $data['results'] = $this->login_model->view_coupondetails();
                    $this->session->set_flashdata('success', 'Coupon created successfully!!!');
                    $data['active'] = 7;
                    redirect('insert-coupons');
                } else {
                    $data['results'] = $this->login_model->view_coupondetails();
                    $this->load->view('inc/header', $data);
                    $this->load->view('coupon/index', $data);
                    $this->load->view('inc/footer');
                }
            } else {
                $itemid = explode("/", $this->input->post('coupon_edate', TRUE));
                $itemid1 = explode("-", $this->input->post('coupon_edate', TRUE));
                $edate = (count($itemid) > 1) ? $itemid[2] . "-" . $itemid[1] . "-" . $itemid[0] : "";
                $insercoupon = array(
                    'coupon_title' => $this->input->post('coupon_title', TRUE),
                    'coupon_code' => $this->input->post('coupon_code', TRUE),
                    'coupon_offername' => $this->input->post('coupon_discount', TRUE),
                    'coupon_rtype' => $this->input->post('coupon_offer', TRUE),
                    'coupon_desc' => $this->input->post('coupon_description', TRUE),
                    'coupon_type' => $this->input->post('coupon_type', TRUE),
                    'coupon_date' => date('Y-m-d'),
                    'coupon_time' => date('H:i:s'),
                    'coupon_edate' => $this->input->post('coupon_edate', TRUE),
                    'coupon_applyuser' => $this->input->post('coupon_user', TRUE)
                );
                if (( $login = $this->login_model->edit_coupondetails($insercoupon, $id)) != false) {
                    $data['results'] = $this->login_model->view_coupondetails();
                    $this->session->set_flashdata('success', 'Coupon Updated successfully!!!');
                    redirect('insert-coupons');
                } else {
                    $data['results'] = $this->login_model->view_coupondetails();
                    $this->load->view('inc/header', $data);
                    $this->load->view('coupon/index', $data);
                    $this->load->view('inc/footer');
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function update_coupon() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 7;
            $id = $this->uri->segment(3);
            $data['result'] = $this->login_model->update_coupondetails($id);
            $this->load->view('inc/header', $data);
            $this->load->view('coupon/add', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired !!! Please Login Again!!!');
            redirect('login');
        }
    }

    function delete_coupon() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
            if ($this->login_model->delete_coupondetail($id) != false) {
                $this->session->set_flashdata('error', 'Deleted successfully!!!');
                redirect('insert-coupons');
            } else {
                $data['results'] = $this->login_model->view_coupondetails();
                redirect('insert-coupons');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function trip_cancel() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 9;
            $this->load->view('inc/header', $data);
            $this->load->view('trip/cancel');
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired Please Login Again');
            header('Location: login');
        }
    }

    function insert_tripcancel() {
        $admin_id = $_SESSION['admin_id'];
        $data['active'] = 9;
        if ($admin_id) {
            $id = $this->uri->segment(3);
            if ($id == "") {
                $insertrip = array(
                    'book_ctype' => $this->input->post('trip_type', TRUE),
                    'book_creason' => $this->input->post('trip_description', TRUE),
                    'status' => $this->input->post('status', TRUE)
                );
                if (( $login = $this->login_model->insert_tripdetails($insertrip)) != false) {
                    $this->session->set_flashdata('success','New Cancel Reason created Successfully');
                    $data['results'] = $this->login_model->view_tripdetails();
                    redirect('insert-tripcancel');
                } else {
                    $data['results'] = $this->login_model->view_tripdetails();
                    $this->load->view('inc/header', $data);
                    $this->load->view('trip/index', $data);
                    $this->load->view('inc/footer');
                }
            } else {
                $insertrip = array(
                    'book_ctype' => $this->input->post('trip_type', TRUE),
                    'book_creason' => $this->input->post('trip_description', TRUE),
                    'status' => $this->input->post('status', TRUE)
                );
                if (( $login = $this->login_model->edit_tripdetails($insertrip, $id)) != false) {
                    $data['results'] = $this->login_model->view_tripdetails();
                    $this->session->set_flashdata('success','Cancel Reason updated Successfully');
                    redirect('insert-tripcancel');
                } else {
                    $data['results'] = $this->login_model->view_tripdetails();
                    $this->load->view('inc/header', $data);
                    $this->load->view('trip/index', $data);
                    $this->load->view('inc/footer');
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired Please Login Again');
            redirect('login');
        }
    }

    function update_tripreason() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 9;
            $id = $this->uri->segment(3);
            $data['result'] = $this->login_model->update_tripdetails($id);
            $this->load->view('inc/header', $data);
            $this->load->view('trip/cancel', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired Please Login Again !!!');
            redirect("login");
        }
    }

    function delete_tripreason() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
            if ($this->login_model->delete_tripdetail($id) != false) {
                $data['results'] = $this->login_model->view_tripdetails();
                $this->session->set_flashdata('success', 'Delete successfully!!!');
                redirect('insert-tripcancel');
            } else {
                $data['results'] = $this->login_model->view_tripdetails();
                redirect('insert-tripcancel');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function add_subscription() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 8;
            $this->load->view('inc/header', $data);
            $this->load->view('subscription/add');
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('Session Expired !!! Please Login Again!!!');
            redirect("login");
        }
    }

    function insert_subscription() {
        $admin_id = $_SESSION['admin_id'];
        $data['active'] = 8;
        if ($admin_id) {
            $id = $this->uri->segment(3);
            if ($id == "") {
                $type = $this->input->post('subscribe_type', TRUE);
                if ($type == "Rs") {
                    $finalamount = $this->input->post('subscribe_amount', TRUE) - $this->input->post('subscribe_offer', TRUE);
                } else {
                    $t = (( $this->input->post('subscribe_offer', TRUE) / 100) * $this->input->post('subscribe_amount', TRUE));
                    $finalamount = $this->input->post('subscribe_amount', TRUE) - $t;
                }
                if($this->input->post('subscribe_amount', TRUE)!=null) {
                if ($finalamount <= 0) {
                    $this->session->set_flashdata('error','Invalid Discount Amount');
                    redirect('login/add_subscription');
                }
            }
                $insersubscription = array(
                    'subscribe_name' => $this->input->post('subscribe_name', TRUE),
                    'subscribe_amount' => number_format($this->input->post('subscribe_amount', TRUE), 2),
                    'subscribe_offer' => ($type=='Rs') ? number_format($this->input->post('subscribe_offer', TRUE),2) : $this->input->post('subscribe_offer', TRUE),
                    'subscribe_days' => $this->input->post('subscribe_days', TRUE),
                    'subscribe_type' => $this->input->post('subscribe_type', TRUE),
                    'subscribe_finalamt' => number_format($finalamount, 2),
                    'subscribe_applydate' => date('Y-m-d'),
                    'subscribe_expdate' => date('Y-m-d', strtotime('+' . $this->input->post('subscribe_days', TRUE) . ' days'))
                );
                if (( $login = $this->login_model->insert_subscriptiondetails($insersubscription)) != false) {
                    $data['results'] = $this->login_model->view_subscriptiondetails();
                    $this->session->set_flashdata('success', 'New Subscription is created Successfully');
                    redirect('insert-subscription');
                } else {
                    $data['results'] = $this->login_model->view_subscriptiondetails();
                    $this->load->view('inc/header', $data);
                    $this->load->view('subscription/index', $data);
                    $this->load->view('inc/footer');
                }
            } else {
                $type = $this->input->post('subscribe_type', TRUE);
                if ($type == "Rs") {
                    $finalamount = $this->input->post('subscribe_amount', TRUE) - $this->input->post('subscribe_offer', TRUE);
                } else {
                    $t = (( $this->input->post('subscribe_offer', TRUE) / 100) * $this->input->post('subscribe_amount', TRUE));
                    $finalamount = $this->input->post('subscribe_amount', TRUE) - $t;
                }
                if($this->input->post('subscribe_amount', TRUE)!=null) {
                    if ($finalamount <= 0) {
                        $this->session->set_flashdata('error','Invalid Discount Amount');
                        redirect("login/update_subscription/$id");
                    }
                }
                $insersubscription = array(
                    'subscribe_name' => $this->input->post('subscribe_name', TRUE),
                    'subscribe_amount' => number_format($this->input->post('subscribe_amount', TRUE),2),
                    'subscribe_days' => $this->input->post('subscribe_days', TRUE),
                    'subscribe_offer' => ($type=='Rs') ? number_format($this->input->post('subscribe_offer', TRUE),2) : $this->input->post('subscribe_offer', TRUE),
                    'subscribe_type' => $this->input->post('subscribe_type', TRUE),
                    'subscribe_finalamt' => number_format($finalamount,2),
                    'subscribe_applydate' => date('Y-m-d'),
                    'subscribe_expdate' => date('Y-m-d', strtotime('+' . $this->input->post('subscribe_days', TRUE) . ' days'))
                );
                if (( $login = $this->login_model->edit_subscriptiondetails($insersubscription, $id)) != false) {
                    $data['results'] = $this->login_model->view_subscriptiondetails();
                    $this->session->set_flashdata('success', 'Subscription Updated successfully!!!');
                    redirect('insert-subscription');
                } else {
                    $data['results'] = $this->login_model->view_subscriptiondetails();
                    redirect('insert-subscription');
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function update_subscription() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 8;
            $id = $this->uri->segment(3);
            $data['result'] = $this->login_model->update_subscriptiondetails($id);
            $this->load->view('inc/header', $data);
            $this->load->view('subscription/add', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('Session Expired !!! Please Login Again!!!');
            redirect("login");
        }
    }

    function delete_subscription() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
            if ($this->login_model->delete_subscriptiondetail($id) != false) {
                $this->session->set_flashdata('error', 'Delete successfully!!!');
                redirect('insert-subscription');
            } else {
                $this->session->set_flashdata('error', 'Please Try Again !!!');
                redirect('insert-subscription');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function delete_riderlist() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
            if ($this->login_model->delete_rider_list($id) != false) {
                $data['results'] = $this->login_model->get_rider_list();
                $this->session->set_flashdata('success', 'Deleted successfully!!!');
                redirect('rider-list');
            } else {
                $data['results'] = $this->login_model->get_rider_list();
                redirect('rider-list');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }
    function logout_riderlist() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
            if ($this->login_model->logout_rider_list($id) != false) {
                $this->push_deactivateAdminRider($id);
                $s = $this->webservice_model->gcm_logout_process($id, 'Rider', '0');
                $this->session->set_flashdata('success','updated successfully');
                redirect('rider-list');
            } else {
                $data['results'] = $this->login_model->get_rider_list();
                redirect('rider-list');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    /*
     *  Section : User- Management
     */

    function user_list() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id != null) {
            $data['results'] = $this->login_model->get_user_list();
            $data['active'] = 3;
            $this->load->view('inc/header', $data);
            $this->load->view('user/index', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect("login");
        }
    }

    function add_user() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id != null) {
            $data['active'] = 3;
            $this->load->view('inc/header', $data);
            $this->load->view('user/add');
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect("login");
        }
    }

    function insert_users() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
                $mbno = $this->input->post('user_mobileno', TRUE);
                $embno=$this->input->post('user_emobileno',TRUE);
            if ($id == "") {
                $email=strtolower($this->input->post('user_email', TRUE));
                $password=md5($this->input->post('user_password', TRUE));
                $confirmpassword=md5($this->input->post('user_cpassword', TRUE));
                if($password!=$confirmpassword) {
                    $this->session->set_flashdata('error','Password and confirm password does not match Please try again !!!');
                    redirect('login/add_user');
                }
                if($mbno==$embno) {
                    $this->session->set_flashdata('error','Mobile number and Emergency Mobileno Should not be Same !!!');
                    redirect('login/add_user');
                }
                $user_details = $this->login_model->mobileCheckUsers('user_registration',$email, $mbno);
                if (!$user_details) {
                    //$otp = rand(100000, 999999);
                    $otp = 1234;
                    $userdata = array(
                        'user_name' => $this->input->post('user_name', TRUE),
                        'user_mobileno' => $mbno,
                        'user_gender' => $this->input->post('user_gender', TRUE),
                        'user_email' => $email,
                        'user_password' => md5($this->input->post('user_password', TRUE)),
                        'user_ename' => $this->input->post('user_econtactname', TRUE),
                        'user_emobileno' => $this->input->post('user_emobileno', TRUE),
                        'user_otp' => $otp,
                        'user_date' => date('Y-m-d'),
                        'user_time' => date('H:i:s')
                    );
                    $user_id=$this->login_model->user_registration_proces($userdata);
                    if ($user_id) {
                        $this->session->set_userdata('user_otp_id', $user_id);
                        $dlr_url = "";
                        $type = "xml";
                        $time = '';
                        $unicode = '';
                        $to = $mbno;
                        $message = "Your%20PiggyBack%20verification%20code%20:%20" . $otp;
                        $url = "http://hpsms.dial4sms.com/api/web2sms.php?workingkey=Aee31e1af01a8d305496d795179a5a8a0&sender=HTHBKR&to=$to&message=$message&type=$type&dlr_url=$dlr_url$time$unicode";
                        //$url = 'http://hpsms.dial4sms.com/api/web2sms.php?username=hthbkrds&password=Admin@17&to=$m&sender=HTHBKR&message=$g';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $output = curl_exec($ch);
                        if (curl_errno($ch)) {
                            echo 'error:' . curl_error($ch);
                        }
                        curl_close($ch);
                        $this->user_otp_page($user_id);
                    }
                } else {
                    $this->session->set_flashdata('error', 'Email-Id or Mobile Number Already Exists!!!');
                    redirect('login/add_user');
                }
            } else {
//                $otp = rand(100000, 999999);
                $otp = 1234;
                $user = $this->uri->segment(3);
                $mbno = $this->input->post('user_mobileno', TRUE);
                if($mbno==$embno) {
                    $this->session->set_flashdata('error','Mobile number and Emergency Mobile number Should not be Same !!!');
                    redirect('login/update_user_detail/'.$user);
                }
                $result = $this->login_model->update_user_detail($id);
                if ($result->user_mobileno == $mbno) {
                    $userdata = array(
                        'user_name' => $this->input->post('user_name', TRUE),
                        'user_mobileno' => $this->input->post('user_mobileno', TRUE),
                        'user_gender' => $this->input->post('user_gender', TRUE),
                        'user_email' => $this->input->post('user_email', TRUE),
                        'user_ename' => $this->input->post('user_econtactname', TRUE),
                        'user_emobileno' => $this->input->post('user_emobileno', TRUE),
                        'user_date' => date('Y-m-d'),
                        'user_time' => date('H:i:s')
                    );
                    $ty = $this->webservice_model->user_registration_updateprocess($userdata, $user);
                    $this->session->set_flashdata('success','The user details updated successfully');
                    redirect('login/user_list');
                } else {
                    $checkmobile=$this->login_model->mobileCheckUsers('user_registration', '', $mbno);
                    if($checkmobile) {
                        $this->session->set_flashdata('error', 'Mobile number already Exist!!!');
                        redirect("login/update_user_detail/$id");
                    } else {
                    $userdata = array(
                        'user_name' => $this->input->post('user_name', TRUE),
                        'user_mobileno' => $this->input->post('user_mobileno', TRUE),
                        'user_gender' => $this->input->post('user_gender', TRUE),
                        'user_email' => $this->input->post('user_email', TRUE),
                        'user_ename' => $this->input->post('user_econtactname', TRUE),
                        'user_emobileno' => $this->input->post('user_emobileno', TRUE),
                        'user_otp' => $otp,
                        'user_otpverify' =>'0',
                        'user_date' => date('Y-m-d'),
                        'user_time' => date('H:i:s')
                    );
                    $ty = $this->webservice_model->user_registration_updateprocess($userdata, $user);
                   $dlr_url = "";
                   $type = "xml";
                   $time = '';
                   $unicode = '';
                   $to = $mbno;
                   $message = "Your%20PiggyBack%20verification%20code%20:%20" . $otp;
                   $url = "http://hpsms.dial4sms.com/api/web2sms.php?workingkey=Aee31e1af01a8d305496d795179a5a8a0&sender=HTHBKR&to=$to&message=$message&type=$type&dlr_url=$dlr_url$time$unicode";
                   //$url = 'http://hpsms.dial4sms.com/api/web2sms.php?username=hthbkrds&password=Admin@17&to=$m&sender=HTHBKR&message=$g';
                   $ch = curl_init();
                   curl_setopt($ch, CURLOPT_URL, $url);
                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                   $output = curl_exec($ch);
                   if (curl_errno($ch)) {
                       echo 'error:' . curl_error($ch);
                   }
                   curl_close($ch);
                    }
                    $this->user_otp_page($id);
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }
    function user_otp_page($id) {
        $data['active'] = 3;
        $data['id'] = $id;
        $this->load->view('inc/header', $data);
        $this->load->view('user/otp', $data);
        $this->load->view('inc/footer');
    }

    function insert_user_otp() {
        $admin_id = $_SESSION['admin_id'];
        $id = $this->uri->segment(3);
        if ($admin_id) {
            $otp = $this->input->post('user_otp', TRUE);
            $type = $this->input->post('type', TRUE);
            $s = $this->login_model->user_otp_verify($otp, $type);
            if ($type == "Rider") {
                if ($s) {
                    $this->session->set_flashdata('success', 'Mobile Number is verified successfully');
                    redirect('login/rider_list');
                } else {
                    $this->session->set_flashdata('error', 'Invalid OTP Please try Again!!!');
                    $this->rider_otp_page($id);
                }
            } else {
                if ($s) {
                    $this->session->set_flashdata('success', 'Mobile Number is verified successfully');
                    redirect('login/user_list');
                } else {
                    $this->session->set_flashdata('error', 'Invalid OTP Please try Again!!!');
                    $this->user_otp_page($id);
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired !!! Please Login Again !!!');
            redirect('login');
        }
    }

    function resend_otp() {
        $admin_id = $_SESSION['admin_id'];
            $usertype = $this->uri->segment(3);
            //$otp = rand(100000, 999999);
            $otp = 1234;
        if($usertype=='user') {
            $user_otp_id=$_SESSION['user_otp_id'];
            $user_id=$this->login_model->update_resend_otp($otp,$user_otp_id);
            $user_details=$this->login_model->get_user_name($user_otp_id);
            $mobileno=$user_details->user_mobileno;
        } else {
            $user_otp_id=$_SESSION['rider_otp_id'];
            $user_id=$this->login_model->update_resend_otpRider($otp,$user_otp_id);
            $user_details=$this->login_model->get_rider_name($user_otp_id);
            $mobileno=$user_details->rider_mobileno;
        }
        if ($admin_id) {
                   $dlr_url = "";
                   $type = "xml";
                   $time = '';
                   $unicode = '';
                   $to = $mobileno;
                   $message = "Your%20PiggyBack%20verification%20code%20:%20" . $otp;
                   $url = "http://hpsms.dial4sms.com/api/web2sms.php?workingkey=Aee31e1af01a8d305496d795179a5a8a0&sender=HTHBKR&to=$to&message=$message&type=$type&dlr_url=$dlr_url$time$unicode";
                   //$url = 'http://hpsms.dial4sms.com/api/web2sms.php?username=hthbkrds&password=Admin@17&to=$m&sender=HTHBKR&message=$g';
                   $ch = curl_init();
                   curl_setopt($ch, CURLOPT_URL, $url);
                   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                   $output = curl_exec($ch);
                   if (curl_errno($ch)) {
                       echo 'error:' . curl_error($ch);
                   }
                   curl_close($ch);
            $this->session->set_flashdata('success','OTP is send Again!!!');
            if($usertype=='user') {
                $this->user_otp_page($user_otp_id);
            }else {
                $this->rider_otp_page($user_otp_id);
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired !!! Please Login Again !!!');
            redirect('login');
        }
    }

    function delete_user_detail() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
            if ($this->login_model->delete_user_detail($id) != false) {
                $data['results'] = $this->login_model->get_user_list();
                $this->session->set_flashdata('success', 'Deleted successfully!!!');
                redirect('login/user_list');
            } else {
                redirect('login/user_list');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }
    function logout_user_detail() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $id = $this->uri->segment(3);
            if ($this->login_model->logout_user_detail($id) != false) {
                $this->push_deactivateByAdmin($id);
                $s = $this->webservice_model->gcm_logout_process($id, 'User', '0');
                $this->session->set_flashdata('success', 'updated successfully!!!');
                redirect('login/user_list');
            } else {
                redirect('login/user_list');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function update_user_detail() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 3;
            $id = $this->uri->segment(3);
            $data['result'] = $this->login_model->update_user_detail($id);
            $this->load->view('inc/header', $data);
            $this->load->view('user/add', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function add_rider() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 4;
            $this->load->view('inc/header', $data);
            $this->load->view('rider/add');
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function insert_riders() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 4;
            $id = $this->uri->segment(3);
            $rider_email = strtolower($this->input->post('rider_email', TRUE));
            $rider_mobileno = $this->input->post('rider_mobileno', TRUE);
            $rider_liceneno = $this->input->post('rider_Licenseno', TRUE);
            $rider_vehicle_bikeno = $this->input->post('rider_bikeno', TRUE);
            $rider_emobileno = $this->input->post('rider_emobileno', TRUE);
            if ($id == "") {
                if($rider_mobileno==$rider_emobileno) {
                    $this->session->set_flashdata('error','Mobile number and Emergency number should not be Same !!!');
                    redirect('login/add_rider');
                }
                $password=md5($this->input->post('rider_password', TRUE));
                $confirmpassword=md5($this->input->post('rider_cpassword', TRUE));
                if($password!=$confirmpassword) {
                    $this->session->set_flashdata('error','Password and confirm password does not match Please try again !!!');
                    redirect('login/add_rider');
                }
                $result = $this->login_model->mobileCheck($rider_email, $rider_mobileno);
                if ($result != null) {
                    $resultset = $this->webservice_model->rider_registration_check($rider_email, $rider_mobileno, $rider_liceneno, $rider_vehicle_bikeno);
                    if ($result->rider_email == $rider_email) {
                        $this->session->set_flashdata('error', 'Email-Id Already Exist in Rider!!!');
                        redirect('login/add_rider');
                    } else if ($result->rider_mobileno == $rider_mobileno) {
                        $this->session->set_flashdata('error', 'Mobile Number Already Exist in Rider!!!');
                        redirect('login/add_rider');
                    } else if (isset($resultset) && ($resultset->rider_liceneno == $rider_liceneno)) {
                        $this->session->set_flashdata('error', 'License Number already Exist!!!');
                        redirect('login/add_rider');
                    } else if (isset($resultset) && ($resultset->rider_vehicle_bikeno == $rider_vehicle_bikeno)) {
                        $this->session->set_flashdata('error', 'Bike Number already Exist!!!');
                        redirect('login/add_rider');
                    }
                } else {
                    $today=date('Y-m-d');
                    $end_date=date('Y-m-d', strtotime($this->input->post('end_date')));
                    if($end_date < $today) {
                        $this->session->set_flashdata('error','End date is expired in Insurance Please Try  again');
                        redirect("login/add_rider");
                    }
                    $referralcode = $this->input->post('rider_referralcode', TRUE)!=null ? $referralcode : 'HB'.rand(100000, 999999);
                    $mbno = $this->input->post('rider_mobileno', TRUE);
                    //$otp = rand(100000, 999999);
                    $otp = 1234;
                    $riderdata = array(
                        'rider_referralcode' => $referralcode,
                        'rider_name' => $this->input->post('rider_name', TRUE),
                        'rider_mobileno' => $this->input->post('rider_mobileno', TRUE),
                        'rider_gender' => $this->input->post('rider_gender', TRUE),
                        'rider_email' => strtolower($this->input->post('rider_email', TRUE)),
                        'rider_password' => md5($this->input->post('rider_password', TRUE)),
                        'rider_liceneno' => strtoupper($this->input->post('rider_Licenseno', TRUE)),
                        'rider_profession' => $this->input->post('rider_profession', TRUE),
                        'rider_address' => $this->input->post('rider_address', TRUE),
                        'rider_vehicle_manufacturing' => $this->input->post('rider_bikename', TRUE),
                        'rider_vehicle_model' => $this->input->post('rider_bikemodel', TRUE),
                        'rider_vehicle_year' => $this->input->post('rider_year', TRUE),
                        'rider_vehicle_color' => $this->input->post('rider_bikecolor', TRUE),
                        'rider_vehicle_bikeno' => strtoupper($this->input->post('rider_bikeno', TRUE)),
                        'rider_econtactname' => $this->input->post('rider_econtactname', TRUE),
                        'rider_emobileno' => $this->input->post('rider_emobileno', TRUE),
                        'rider_otp' => $otp,
                        'rider_date' => date('Y-m-d'),
                        'rider_time' => date('H:i:s')
                    );
                    if ($this->webservice_model->rider_registration_proces($riderdata) != false) {
                        $riderid = $this->webservice_model->getriderid();
                        $bookid1 = $riderid->rider_id; // $bookid1 is RiderId.!!!
                        $this->session->set_userdata('rider_otp_id',$bookid1);
                        if ($bookid1) {
                            $this->Rider_insurance_model->insert(array(
                                'rider_id' => $bookid1, // Rider ID
                                'policy_number' => $this->input->post('policy_number'),
                                'start_date' => date('Y-m-d', strtotime($this->input->post('start_date'))),
                                'end_date' => date('Y-m-d', strtotime($this->input->post('end_date'))),
                                'status' => '1'
                            ));
                        }
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

                        $v = $this->webservice_model->rider_update_referralcode($bookid1, $code, $received);
                        $bookid1;
                        $i = md5(time()).$bookid1;
                        $image = addslashes($_FILES['rider_profile']['tmp_name']);
                        if ($image != null) {
                            $image = file_get_contents($image);
                            $dataUri = base64_encode($image);
                            $data1 = 'data:image/png;base64,' . $dataUri . '';
                            list($type, $data1) = explode(';', $data1);
                            list(, $data1) = explode(',', $data1);
                            $data1 = base64_decode($data1);
                            $profile = "Profile_" .$i . ".png";
                        }
                        $image1 = addslashes($_FILES['rider_license']['tmp_name']);
                        if ($image1 != null) {
                            $image1 = file_get_contents($image1);
                            $dataUri1 = base64_encode($image1);
                            $data2 = 'data:image/png;base64,' . $dataUri1 . '';
                            list($type, $data2) = explode(';', $data2);
                            list(, $data2) = explode(',', $data2);
                            $data2 = base64_decode($data2);
                            $licene = "Licence_" . $i . ".png";
                        }
                        $image2 = addslashes($_FILES['rider_insurance']['tmp_name']);
                        if ($image2 != null) {
                            $image2 = file_get_contents($image2);
                            $dataUri2 = base64_encode($image2);
                            $data3 = 'data:image/png;base64,' . $dataUri2 . '';
                            list($type, $data3) = explode(';', $data3);
                            list(, $data3) = explode(',', $data3);
                            $data3 = base64_decode($data3);
                            $insurance = "Insurance_" .$i . ".png";
                        }
                        $image3 = addslashes($_FILES['rider_rccopy']['tmp_name']);
                        if ($image3 != null) {
                            $image3 = file_get_contents($image3);
                            $dataUri3 = base64_encode($image3);
                            $data4 = 'data:image/png;base64,' . $dataUri3 . '';
                            list($type, $data4) = explode(';', $data4);
                            list(, $data4) = explode(',', $data4);
                            $data4 = base64_decode($data4);
                            $rcbook = "Rcbook_".$i . ".png";
                        }
                        $image5 = ($_FILES['rider_aadhar']['tmp_name']!=null) ? addslashes($_FILES['rider_aadhar']['tmp_name']) : "";
                        if ($image5 != null) {
                            $image5 = file_get_contents($image5);
                            $dataUri5 = base64_encode($image1);
                            $data5 = 'data:image/png;base64,' . $dataUri5 . '';
                            list($type, $data5) = explode(';', $data5);
                            list(, $data5) = explode(',', $data5);
                            $data5 = base64_decode($data5);
                            $aadhar = "Aadhar_" .$i . ".png";
                        }
                        file_put_contents('./uploads/profile/' . "Profile_" . $i . '.png', (isset($data1)) ? $data1 : "");
                        file_put_contents('./uploads/License/' . "Licence_" . $i . '.png', (isset($data2)) ? $data2 : "");
                        file_put_contents('./uploads/Insurance/' . "Insurance_" . $i . '.png', (isset($data3)) ? $data3 : "");
                        file_put_contents('./uploads/Rcbook/' . "Rcbook_" . $i . '.png', (isset($data4)) ? $data4 : "");
                        file_put_contents('./uploads/aadhar/' . "Aadhar_" . $i . '.png', (isset($data5)) ? $data5 : "");

                        $this->load->library('image_lib');
                        if (isset( $profile)) {
                            $config['image_library'] = 'gd2';
                            $config['source_image'] = './uploads/profile/' . "Profile_" . $i . '.png';
                            $config['height'] = "50";
                            $config['width'] = "50";
                            $config['new_image'] = './tempuploads/Tempprofile/';
                            $this->image_lib->initialize($config);
                            $this->image_lib->resize();
                        }
                        if (isset($licene)) {
                            $config['image_library'] = 'gd2';
                            $config['source_image'] = './uploads/License/' . "Licence_" . $i . '.png';
                            $config['height'] = "50";
                            $config['width'] = "50";
                            $config['new_image'] = './tempuploads/TempLicense/';
                            $this->image_lib->initialize($config);
                            $this->image_lib->resize();
                        }
                        if (isset($insurance)) {
                            $config['image_library'] = 'gd2';
                            $config['source_image'] = './uploads/Insurance/' . "Insurance_" . $i . '.png';
                            $config['height'] = "50";
                            $config['width'] = "50";
                            $config['new_image'] = './tempuploads/TempInsurance/';
                            $this->image_lib->initialize($config);
                            $this->image_lib->resize();
                        }
                        if (isset($rccopy)) {
                            $config['image_library'] = 'gd2';
                            $config['source_image'] = './uploads/Rcbook/' . "Rcbook_" . $i . '.png';
                            $config['height'] = "50";
                            $config['width'] = "50";
                            $config['new_image'] = './tempuploads/TempRcbook/';
                            $this->image_lib->initialize($config);
                            $this->image_lib->resize();
                        }
                        $riderImage=array(
                            'rider_picture' => (isset($profile)) ? $profile : "",
                            'rider_aadhar' => (isset($aadhar)) ? $aadhar : "",
                            'rider_licenecopy' => (isset($licene)) ? $licene : "",
                            'rider_insurancecopy' => (isset($insurance)) ? $insurance : "",
                            'rider_rcbookcopy' => (isset($rcbook)) ? $rcbook : "",
                        );
                        $t = $this->login_model->rider_image_update($riderImage, $bookid1);
                       $dlr_url = "";
                       $type = "xml";
                       $time = '';
                       $unicode = '';
                       $to = $mbno;
                       $message = "Your%20PiggyBack%20verification%20code%20:%20" . $otp;
                       $url = "http://hpsms.dial4sms.com/api/web2sms.php?workingkey=Aee31e1af01a8d305496d795179a5a8a0&sender=HTHBKR&to=$to&message=$message&type=$type&dlr_url=$dlr_url$time$unicode";
                       //$url = 'http://hpsms.dial4sms.com/api/web2sms.php?username=hthbkrds&password=Admin@17&to=$m&sender=HTHBKR&message=$g';
                       $ch = curl_init();
                       curl_setopt($ch, CURLOPT_URL, $url);
                       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                       $output = curl_exec($ch);
                       if (curl_errno($ch)) {
                           echo 'error:' . curl_error($ch);
                       }
                       curl_close($ch);
                        $this->rider_otp_page($id);
                    }
                }
            } else {
                $id = $this->uri->segment(3);
                if($rider_mobileno==$rider_emobileno) {
                    $this->session->set_flashdata('error','Mobile number and Emergency number should not be Same !!!');
                    redirect('login/update_riderlist/'.$id);
                }
                //$otp = rand(100000, 999999);
                $otp = 1234;
                $user = $this->uri->segment(3);
                $mbno = $this->input->post('rider_mobileno', TRUE);
                $referralcode = $this->input->post('rider_referralcode', TRUE);
                $rider_id= md5(time()).$id;
                $imagelist  = $_FILES['rider_profile']['name'];
                $imagelist1 = $_FILES['rider_license']['name'];
                $imagelist2 = $_FILES['rider_insurance']['name'];
                $imagelist3 = $_FILES['rider_rccopy']['name'];
                $imagelist4 = $_FILES['rider_aadhar']['name'];
            if ($imagelist != "") {
                $image = addslashes($_FILES['rider_profile']['tmp_name']);
                $image = file_get_contents($image);
                $dataUri = base64_encode($image);
                $data1 = 'data:image/png;base64,' . $dataUri . '';
                list($type, $data1) = explode(';', $data1);
                list(, $data1) = explode(',', $data1);
                $data1 = base64_decode($data1);
                $profile = "Profile_" . $rider_id . ".png";
                $insertimage = array(
                    'rider_picture' => $profile
                );
                $t = $this->login_model->update_profile($insertimage, $id);
                file_put_contents("./uploads/profile/$profile", $data1);
                $s = $this->login_model->update_riderlist($id);
                $dataid = $s->rider_id;
                $dataprofile = $s->rider_picture;
                $this->load->library('image_lib');
                if ($imagelist) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = "./uploads/profile/$profile";
                    $config['height'] = "50";
                    $config['width'] = "50";
                    $config['new_image'] = './tempuploads/Tempprofile/';
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
            }
            if ($imagelist1 != "") {
                $image1 = addslashes($_FILES['rider_license']['tmp_name']);
                $image1 = file_get_contents($image1);
                $dataUri1 = base64_encode($image1);
                $data2 = 'data:image/png;base64,' . $dataUri1 . '';
                list($type, $data2) = explode(';', $data2);
                list(, $data2) = explode(',', $data2);
                $data2 = base64_decode($data2);
                $licene = "Licence_" . $rider_id . ".png";
                $insertimage = array(
                    'rider_licenecopy' => $licene
                );
                $t = $this->login_model->update_profile($insertimage, $id);
                file_put_contents("./uploads/License/$licene", $data2);
                $s = $this->login_model->update_riderlist($id);
                $dataid = $s->rider_id;
                $datalicene = $s->rider_licenecopy;
                $this->load->library('image_lib');
                if ($imagelist1) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = "./uploads/License/$licene";
                    $config['height'] = "50";
                    $config['width'] = "50";
                    $config['new_image'] = './tempuploads/TempLicense/';
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
            }
            if ($imagelist2 != "") {
                $image2 = addslashes($_FILES['rider_insurance']['tmp_name']);
                $image2 = file_get_contents($image2);
                $dataUri2 = base64_encode($image2);
                $data3 = 'data:image/png;base64,' . $dataUri2 . '';
                list($type, $data3) = explode(';', $data3);
                list(, $data3) = explode(',', $data3);
                $data3 = base64_decode($data3);
                $insurance = "Insurance_" . $rider_id . ".png";
                $insertimage = array(
                    'rider_insurancecopy' => $insurance
                );
                $t = $this->login_model->update_profile($insertimage, $id);
                file_put_contents("./uploads/Insurance/$insurance", $data3);
                $s = $this->login_model->update_riderlist($id);
                $dataid = $s->rider_id;
                $datainsurance = $s->rider_insurancecopy;
                $this->load->library('image_lib');
                if ($imagelist2) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = "./uploads/Insurance/$insurance";
                    $config['height'] = "50";
                    $config['width'] = "50";
                    $config['new_image'] = './tempuploads/TempInsurance/';
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
            }
            if ($imagelist3 != "") {
                $image3 = addslashes($_FILES['rider_rccopy']['tmp_name']);
                $image3 = file_get_contents($image3);
                $dataUri3 = base64_encode($image3);
                $data4 = 'data:image/png;base64,' . $dataUri3 . '';
                list($type, $data4) = explode(';', $data4);
                list(, $data4) = explode(',', $data4);
                $data4 = base64_decode($data4);
                $rcbook = "Rcbook_" . $rider_id . ".png";
                $insertimage = array(
                    'rider_rcbookcopy' => $rcbook
                );
                $t = $this->login_model->update_profile($insertimage, $id);
                file_put_contents("./uploads/Rcbook/$rcbook", $data4);
                $s = $this->login_model->update_riderlist($id);
                $dataid = $s->rider_id;
                $datarccopy = $s->rider_rcbookcopy;
                $this->load->library('image_lib');
                if ($imagelist3) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = "./uploads/Rcbook/$rcbook";
                    $config['height'] = "50";
                    $config['width'] = "50";
                    $config['new_image'] = './tempuploads/TempRcbook/';
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
            }
            if ($imagelist4 != "") {
                $image4 = addslashes($_FILES['rider_aadhar']['tmp_name']);
                $image4 = file_get_contents($image4);
                $dataUri4 = base64_encode($image4);
                $data5 = 'data:image/png;base64,' . $dataUri4 . '';
                list($type, $data5) = explode(';', $data5);
                list(, $data5) = explode(',', $data5);
                $data5 = base64_decode($data5);
                $aadhar = "Aadhar_" . $rider_id . ".png";
                $insertimage = array(
                    'rider_aadhar' => $aadhar
                );
                $t = $this->login_model->update_profile($insertimage, $id);
                file_put_contents("./uploads/aadhar/$aadhar", $data5);
                $s = $this->login_model->update_riderlist($id);
                $dataid = $s->rider_id;
                $dataaadhar = $s->rider_aadhar;
                $this->load->library('image_lib');
                if ($imagelist4) {
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = "./uploads/aadhar/$aadhar";
                    $config['height'] = "50";
                    $config['width'] = "50";
                    $config['new_image'] = './tempuploads/Tempaadhar/';
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                }
            }
                $bookid1 = $id;
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

                $v = $this->webservice_model->rider_update_referralcode($bookid1, $code, $received);
                $rider = $id;
                $riderdata = array(
                    'rider_referralcode' => $this->input->post('rider_referralcode', TRUE),
                    'rider_name' => $this->input->post('rider_name', TRUE),
                    //'rider_mobileno' => $this->input->post('rider_mobileno', TRUE),
                    'rider_gender' => $this->input->post('rider_gender', TRUE),
                    'rider_email' => $this->input->post('rider_email', TRUE),
                    //'rider_password' => md5($this->input->post('rider_password', TRUE)),
                    'rider_liceneno' => strtoupper($this->input->post('rider_Licenseno', TRUE)),
                    'rider_profession' => $this->input->post('rider_profession', TRUE),
                    'rider_address' => $this->input->post('rider_address', TRUE),
                    'rider_vehicle_manufacturing' => $this->input->post('rider_bikename', TRUE),
                    'rider_vehicle_model' => $this->input->post('rider_bikemodel', TRUE),
                    'rider_vehicle_year' => $this->input->post('rider_year', TRUE),
                    'rider_vehicle_color' => $this->input->post('rider_bikecolor', TRUE),
                    'rider_vehicle_bikeno' => strtoupper($this->input->post('rider_bikeno', TRUE)),
                    'rider_econtactname' => $this->input->post('rider_econtactname', TRUE),
                    'rider_emobileno' => $this->input->post('rider_emobileno', TRUE),
                    'rider_date' => date('Y-m-d'),
                    'rider_time' => date('H:i:s')
                );
                $ser = $this->webservice_model->rider_registration_updateprocess($riderdata, $rider);
                if ($ser) {
                    $result = $this->Rider_insurance_model->update(array(
                        'rider_id' => $bookid1, // Rider ID
                        'policy_number' => $this->input->post('policy_number'),
                        'start_date' => date('Y-m-d', strtotime($this->input->post('start_date'))),
                        'end_date' => date('Y-m-d', strtotime($this->input->post('end_date'))),
                        'status' => '1'
                            ), $this->input->post('policy_id'));
                    if (!$result) {
                        $this->Rider_insurance_model->insert(array(
                            'rider_id' => $bookid1, // Rider ID
                            'policy_number' => $this->input->post('policy_number'),
                            'start_date' => date('Y-m-d', strtotime($this->input->post('start_date'))),
                            'end_date' => date('Y-m-d', strtotime($this->input->post('end_date'))),
                            'status' => '1'
                        ));
                    }
                }
                $st = $this->login_model->update_riderlist($id);
                if ($st->rider_mobileno == $mbno) {
                    $this->session->set_flashdata('success','The rider details updated successfully!!!');
                    redirect('login/rider_list');
                } else {
                    $result=$this->login_model->mobileonlycheck($mbno,"Rider");
                    if(!$result) {
                    $riderdata = array(
                        'rider_mobileno' => $this->input->post('rider_mobileno', TRUE),
                        'rider_otp' => $otp,
                        'rider_otpverify' =>'0'
                    );
                    $ty = $this->webservice_model->rider_registration_updateprocess($riderdata, $rider);
                    $dlr_url = "";
                    $type = "xml";
                    $time = '';
                    $unicode = '';
                    $to = $mbno;
                    $message = "Your%20PiggyBack%20verification%20code%20:%20" . $otp;
                    $url = "http://hpsms.dial4sms.com/api/web2sms.php?workingkey=Aee31e1af01a8d305496d795179a5a8a0&sender=HTHBKR&to=$to&message=$message&type=$type&dlr_url=$dlr_url$time$unicode";
                    //$url = 'http://hpsms.dial4sms.com/api/web2sms.php?username=hthbkrds&password=Admin@17&to=$m&sender=HTHBKR&message=$g';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $output = curl_exec($ch);
                    if (curl_errno($ch)) {
                        echo 'error:' . curl_error($ch);
                    }
                    curl_close($ch);
                    $this->rider_otp_page($id);
                } else {
                    $this->session->set_flashdata('error', 'Phone number Already Exist in System');
                    redirect("rider-list");
                }
            }

                    }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function rider_otp_page($id) {
        $data['active']=4;
        $data['id']=$id;
        $this->load->view('inc/header', $data);
        $this->load->view('rider/otp', $data);
        $this->load->view('inc/footer');
    }

    function update_riderlist() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['active'] = 4;
            $id = $this->uri->segment(3);
            $data['result'] = $this->login_model->update_riderlist($id);
            if($data['result']!=null) {
            $this->load->view('inc/header', $data);
            $this->load->view('rider/add', $data);
            $this->load->view('inc/footer');
            } else {
                redirect('rider-list');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired ! Please Login Again !!!');
            redirect('login');
        }
    }

    function getbikemodel() {
        $id = $_POST['id'];
        $this->db->select('*');
        $this->db->from('bike_model AS T1');
        $this->db->where('T1.bikemodel_name', $id);
        $query = $this->db->get();
        $array = $query->result();
//        $mbno = $array->agency_id;
        echo json_encode($array);
    }

    function admin_monitoring() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['riders'] = $this->login_model->get_rider_list();
            $data['subscription'] = $this->Subscribe_details_model->get_all();
            $data['active'] = 5;
            $this->load->view('inc/header', $data);
            $this->load->view('adminsubscription/index', $data);
            $this->load->view('inc/footer');
        } else {
            $this->session->set_flashdata('error', 'Please enter valid Name or password');
            redirect("login");
        }
    }

    function gethint() {
        $q = $_REQUEST["q"];
        $this->db->select('*');
        $this->db->from('rider_registration AS T1');
        $this->db->like('T1.rider_name', $q);
        $query = $this->db->get();
        $results = $query->result();
        $data = array();
        foreach ($results as $row) {
            $name = $row->rider_name . "-" . $row->rider_id;
            array_push($data, $name);
        }

        echo json_encode($data);
    }

    function insert_adminsubscription() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $rider_name = $this->input->post('q', TRUE);//Riderid and Name...
            $s = explode("-", $rider_name);
            $subscription_id = $this->input->post('subscription_id', TRUE);
            if($subscription_id) {
                $subscibe_details=$this->Subscribe_details_model->get($subscription_id);
                $rider_details=$this->Rider_registration_model->get($s[1]);
                $rider_subcride_day=($rider_details->rider_subscribe <= 0) ? 0 : $rider_details->rider_subscribe;
                $rider_days = $subscibe_details->subscribe_days + $rider_subcride_day;
                $subscibion_days=$subscibe_details->subscribe_days;
                $subscribe = array(
                    'apply_riderid' => $s[1],
                    'apply_subscribeid' => $subscription_id,
                    'apply_days' => $rider_days,
                    'apply_status' =>'1',
                    'apply_kilometers' => $subscibe_details->subscribe_kilometers,
                    'apply_startdate' => date('Y-m-d'),
                    'apply_enddate' => date('Y-m-d', strtotime('+' . $subscibion_days . ' days')),
                    'apply_paid' => 'HBK_SUCCESS',
                    'apply_paytm_amount' =>$subscibe_details->subscribe_finalamt
                );
                //Function used in Webservice Controller too.!!!
                $sd = $this->webservice_model->insert_subscribe_details($subscribe);
                $subscibe_details=$this->Subscribe_details_model->change_status($sd,$s[1]);
            }
            if ($this->login_model->insert_adminsubscription($rider_name, $rider_days) != false) {
                $this->session->set_flashdata('success', 'Subscription Added successfully!!!');
                redirect('admin-subscription');
            } else {
                $this->session->set_flashdata('error', 'Please select Subscription and try again !!!');
                redirect('admin-subscription');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired !!! Please Login Again!!!');
            redirect("login");
        }
    }

    public function change_password() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $data['user_info'] = $this->login_model->get_admin($admin_id);
            if ($data['user_info']) {
                $this->load->view('inc/header', $data);
                $this->load->view('admin/change_password', $data);
                $this->load->view('inc/footer');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired !!! Please Login Again!!!');
            redirect("login");
        }
    }

    public function update_password() {
        $admin_id = $_SESSION['admin_id'];
        if ($admin_id) {
            $user_info = $this->login_model->get_admin($admin_id);
            if ($user_info) {
                $currentpassword = md5($this->input->post('current_password'));
                if ($currentpassword == $user_info->admin_pwd) {
                    $newpassword = $this->input->post('user_password');
                    $confirmpassword = $this->input->post('confirm_password');
                    if ($newpassword === $confirmpassword) {
                        $results = $this->login_model->admin_update($admin_id, md5($newpassword));
                        if ($results) {
                            $this->session->set_flashdata('success', 'Password Changed Successfully. Please Login Again!!!');
                            redirect('login');
                        } else {
                            redirect('login/change_password');
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Password does not match. Try Again !!!');
                        redirect('login/change_password');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Current Password is Wrong Please try again!!!');
                    redirect('login/change_password');
                }
            } else {
                $this->session->set_flashdata('error', 'Something went wrong Try Again !!!!');
                redirect('login/change_password');
            }
        } else {
            $this->session->set_flashdata('error', 'Session Expired !!! Please Login Again!!!');
            redirect("login");
        }
    }

    function logout() {
        $this->session->unset_userdata('admin_name');
        $this->session->sess_destroy();
        redirect($this->index(), 'refresh');
    }

    function insurance_shedule() {

    }

    function push_deactivateUser($id) {
        $logindetails = $this->webservice_model->getrideriddetail($id->rider_id);
        if ($logindetails) {
            $ids = $logindetails->gcm_userid;
            $apikeyid = $this->webservice_model->getapikey();
            $apiKey = $apikeyid->apikey;
            $registatoin_ids = $logindetails->gcm_regid;
            $message = "Your Deactivated Please Contact Admin";
            $task = "logout_rider";
            $title = "PiggyBack";
            $url = $apikeyid->url;
            $fields = array(
                'to' => $registatoin_ids,
                'priority' => "high",
                'notification' => array("body" => $message, "sound" => "bike_start_up.mp3"),
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

    function push_deactivateAdminRider($id) {
        $logindetails = $this->webservice_model->getrideriddetail($id);
        if ($logindetails) {
            $ids = $logindetails->gcm_userid;
            $apikeyid = $this->webservice_model->getapikey();
            $apiKey = $apikeyid->apikey;
            $registatoin_ids = $logindetails->gcm_regid;
            $message = "Your Deactivated Please Contact Admin";
            $task = "logout_rider";
            $title = "PiggyBack";
            $url = $apikeyid->url;
            $fields = array(
                'to' => $registatoin_ids,
                'priority' => "high",
                'notification' => array("body" => $message, "sound" => "bike_start_up.mp3"),
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

    function push_deactivateByAdmin($id) {
        $logindetails = $this->webservice_model->getuseriddetail($id);
        if ($logindetails) {
            $ids = $logindetails->gcm_userid;
            $apikeyid = $this->webservice_model->getapikey();
            $apiKey = $apikeyid->apikey;
            $registatoin_ids = $logindetails->gcm_regid;
            $message = "Your Deactivated Please Contact Admin";
            $task = "logout_user";
            $title = "PiggyBack";
            $url = $apikeyid->url;
            $fields = array(
                'to' => $registatoin_ids,
                'priority' => "high",
                'notification' => array("body" => $message, "sound" => "bike_start_up.mp3"),
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
