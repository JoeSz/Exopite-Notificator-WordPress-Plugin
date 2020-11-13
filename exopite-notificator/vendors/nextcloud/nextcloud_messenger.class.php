<?php

if ( ! class_exists( 'NextCloud_Messenger' ) ) {

    class NextCloud_Messenger
    {

        public function send_message( $server, $user, $pass, $channel_id, $message ) {

            // notify hack
            $data = array(
                        "token" => $channel_id,
                        "message" => $message,
                        // "actorDisplayName" => "Bot",
                        // "actorType" => "",
                        // "actorId" => "",
                        // "timestamp" => 0,
                        // "messageParameters" => array()
            );

            $payload = json_encode($data);

            $url = $server . '/ocs/v2.php/apps/spreed/api/v1/chat/' . $channel_id;

            $ch = curl_init( $url );

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            // Set HTTP Header
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array (
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($payload),
                    'Accept: application/json',
                    'OCS-APIRequest: true'
                )
            );

            $response = curl_exec($ch);

            curl_close($ch);

            return json_decode($response, true);

        }

    }

}
