<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Firebase_notification
{

    public function sendFirebasePushNotification($notiSubject, $notiBody, $token, $eventID, $documentCode, $documentID, $masterID, $deviceType = 'android')
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        if ($deviceType == 'android') {
            $fields = array(
                'registration_ids' => $token,

                'data' => array(
                    "body" => $notiBody,
                    "title" => $notiSubject,
                    "payload" => array(
                        "id" => (int)$eventID,
                        "documentCode" => $documentCode,
                        "documentID" => $documentID,
                        "masterID" => (int)$masterID
                    )
                )
            );
        } else {
            $fields = array(
                'registration_ids' => $token,

                'notification' => array(
                    "body" => $notiBody,
                    "title" => $notiSubject,
                    "sound" => "default",
                    "payload" => array(
                        "id" => (int)$eventID,
                        "documentCode" => $documentCode,
                        "documentID" => $documentID,
                        "masterID" => (int)$masterID
                    )
                ),
            );
        }

        $fields = json_encode($fields);
//
        if (hstGeras == 1) {
            $headers = array(
                'Authorization: key=' . "AAAAAEIm4PA:APA91bEw3ydbMmOcwB7y3v33hJIFL76AhhUlzE0VEYfG_RJGIJCp5SMGimQF5IxHNm_4vTQF8EtNjbRTFI5sOYOy5Upoqr2P1y6jMU4lggUp1kCBcisM85R_WZnKvatDp94RE5fYkhcu",
                'Content-Type: application/json'
            );
        } else {
            $headers = array(
                'Authorization: key=' . "AAAATUGBd4o:APA91bHRQakQemFAsFbSR9ZZn2MpNlXTkHzBu6msvPHktpOLcaJ3HHhHLeD-_NCO-0Nb5BDC7MbLyzcluAP7MJ2dvaYpwm5ye0LpkSA-jdRDjCgqHIcNHtMqKYLPmFNg1wZWMv4v4S9l",
                'Content-Type: application/json'
            );
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }
}
