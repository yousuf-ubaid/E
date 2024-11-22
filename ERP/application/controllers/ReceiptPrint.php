<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class ReceiptPrint extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function oAuthRedirect(){
        $redirectConfig = array(
            'client_id' 	=> '612603340611-2ekrg4u0tq67jr7gtcqck55qmea0uj2c.apps.googleusercontent.com',
            'redirect_uri' 	=> STATIC_LINK.'/index.php/ReceiptPrint/oAuthRedirect',
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/cloudprint',
        );

        $authConfig = array(
            'code' => '',
            'client_id' 	=> '612603340611-2ekrg4u0tq67jr7gtcqck55qmea0uj2c.apps.googleusercontent.com',
            'client_secret' => 'YLYzYgnBS796RGngjCisxPTC',
            'redirect_uri' 	=> STATIC_LINK.'/index.php/ReceiptPrint/oAuthRedirect',
            "grant_type"    => "authorization_code"
        );

        $offlineAccessConfig = array(
            'access_type' => 'offline'
        );

        $refreshTokenConfig = array(

            'refresh_token' => "",
            'client_id' => $authConfig['client_id'],
            'client_secret' => $authConfig['client_secret'],
            'grant_type' => "refresh_token"
        );

        $urlconfig = array(
            'authorization_url' 	=> 'https://accounts.google.com/o/oauth2/auth',
            'accesstoken_url'   	=> 'https://accounts.google.com/o/oauth2/token',
            'refreshtoken_url'      => 'https://www.googleapis.com/oauth2/v3/token'
        );


        if (isset($_GET['op'])) {

            if ($_GET['op']=="getauth") {
                header("Location: ".$urlconfig['authorization_url']."?".http_build_query($redirectConfig));
                exit;
            }
            else if ($_GET['op']=="offline") {
                header("Location: ".$urlconfig['authorization_url']."?".http_build_query(array_merge($redirectConfig,$offlineAccessConfig)));
                exit;
            }
        }

        // Google redirected back with code in query string.
        if(isset($_GET['code']) && !empty($_GET['code'])) {
            $code = $_GET['code'];
            $authConfig['code'] = $code;

			$this->load->library("CloudPrints");
            // Create object
            $accessToken = $this->cloudprints->getToken($urlconfig['accesstoken_url'],$authConfig);
            if(isset($accessToken->error)){
                echo $accessToken->error_description;
            }else{
                $session_data = array(
                    'accessToken' => $accessToken->access_token,
                );
                $this->session->set_userdata($session_data);
                redirect('restaurant');
            }
        }
    }


}
