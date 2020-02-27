<?php


class Api {

    public $basicAuth = '';
    public $options = [];
    
    public function __construct($baseUrl='', $defaultHeaders=[], $curlOptions=[]){
        $this->options = array(
            CURLOPT_URL => $baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => '',
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $defaultHeaders,
            CURLOPT_POSTFIELDS => '',
        );
        if(count($curlOptions)){
            $this->options = $curlOptions + $this->options;
        }
    }

    public function setBasicAuth($login, $password){
        $this->basicAuth = $login . ":" . $password;
    }

    public function post($url, $vars='', $headers=[]){
        return $this->request('POST', $url, $vars, $headers);
    }

    public function put($url, $vars='', $headers=[]){
        return $this->request('PUT', $url, $vars, $headers);
    }

    public function delete($url, $headers=[]){
        return $this->request('DELETE', $url, '', $headers);
    }

    public function get($url, $headers=[]){
        return $this->request('GET', $url, '', $headers);
    }

    public function request($type, $url, $vars='', $headersX = []){
        $this->options[CURLOPT_URL] .= $url;
        $this->options[CURLOPT_CUSTOMREQUEST] = $type;
        $this->options[CURLOPT_POSTFIELDS] .= $vars;
        
        if(count($headersX)){
            $this->options[CURLOPT_HTTPHEADER] = $headersX + $this->options[CURLOPT_HTTPHEADER];
        }

        if($this->options[CURLOPT_POSTFIELDS] != ''){
            $this->options[CURLOPT_HTTPHEADER][] = 'Content-Length: '.strlen($this->options[CURLOPT_POSTFIELDS]);
        }

        if($this->basicAuth != ''){
            $this->options = array(CURLOPT_USERPWD => $this->basicAuth) + $this->options;
        }

        $curl = curl_init();
        curl_setopt_array($curl, $this->options);

        $info_request = curl_getinfo($curl);
        $response = curl_exec($curl);
        $ret = array(
            'request' => $info_request,
            'options' => $this->options,
            'response' => $response,
            'error' => curl_error($curl),
            'info' => curl_getinfo($curl),
        );
        curl_close($curl);

        return $ret;
    }

}
