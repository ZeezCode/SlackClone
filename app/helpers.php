<?php
    /**
     * Given ID of Imgur image, returns if the image exists and is valid
     * @param $id
     * @return array
     */
    function isImgurIdValid($id) {
        $result = [];
        $result['isValid'] = true;
        
        if (!ctype_alnum($id)) { //curl gets mad and explodes if the id isn't alphanumeric
            $result['isValid'] = false;
        } else {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.imgur.com/3/image/" . $id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Client-ID 052c0eea2aa262a"
                ),
            ));
    
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
    
            if ($err) { //Error with curl, reject image for now
                $result['isValid'] = false;
            } else { //Got response
                $json = json_decode($response);
                
                $result['isValid'] = $json->success;
                if ($json->success)
                    $result['link'] = $json->data->link;
            }
        }
        return $result;
    }