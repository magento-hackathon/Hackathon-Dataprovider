<?php

class Hackathon_Dataprovider_Helper_Data extends Mage_Core_Helper_Data
{
	public function __construct() {
        $this->version = '0.1';
        $this->secure = true;
        $this->url = 'http' . ($this->secure ? 's' : null) . '://www.dataprovider.com/api/' . $this->version . '/lookup/';
        $this->api_key = Mage::getStoreConfig('dataprovider/general/apikey');
    }

    /* These functions are code-wise the same, the only thing that differs is the function name itself and the parameters they take */
    public function hostname($name) {
        $args = get_defined_vars();
        $url = $this->url . __FUNCTION__ . '.json?api_key=' . $this->api_key;
        foreach($args as $key=>$value) {
            $url .= '&'.$key.'='.$value;
        }
        return $this->request($url);
    }
    
    public function zipcode($zipcode,$housenumber=null,$country='NL') {
        $country = strtoupper($country);
        if($country=='NL' && strlen($zipcode)==6) $zipcode = substr($zipcode,0,4) . ' ' . substr($zipcode,4);
        $args = get_defined_vars();
        unset($args['housenumber']);
        $url = $this->url . __FUNCTION__ . '.json?api_key=' . $this->api_key;
        foreach($args as $key=>$value) {
            $url .= '&'.$key.'='.urlencode($value);
        }
        $result = $this->request($url);
        if($housenumber==null) {
            return $result;
        } else {
            foreach($result->data as $key=>$data) {
                if(!empty($data->address)) {
                    $split = $this->splitAddress($data->address);
                    if($split!==false) {
                        if(strtolower($housenumber)!=strtolower($split[1])) {
                            unset($result->data[$key]);
                        }
                    }
                } else {
                    unset($result->data[$key]);
                }
            }
            return $result->data;
        }
        return false;
    }
    
    public function phone($number,$country='nl',$all=true) {
        $args = get_defined_vars();
        $url = $this->url . __FUNCTION__ . '.json?api_key=' . $this->api_key;
        foreach($args as $key=>$value) {
            $url .= '&'.$key.'='.$value;
        }
        return $this->request($url);
    }
    
    public function chamberofcommerce($number) {
        $args = get_defined_vars();
        $url = $this->url . __FUNCTION__ . '.json?api_key=' . $this->api_key;
        foreach($args as $key=>$value) {
            $url .= '&'.$key.'='.$value;
        }
        return $this->request($url);
    }
    
    public function tax($number) {
        $args = get_defined_vars();
        $url = $this->url . __FUNCTION__ . '.json?api_key=' . $this->api_key;
        foreach($args as $key=>$value) {
            $url .= '&'.$key.'='.$value;
        }
        return $this->request($url);
    }

    /* Private request function */
    private function request($url) {
        $ch = curl_init($url);
        $headers = array(
            'Content-Type: application/json'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if(!$output || strlen($output)==1 || $code!='200') {
            if($code!='200') {
                throw new Exception('Error (code '.$code.', URL '.$url.'). ' . curl_error($ch));
            } else {
                throw new Exception('Error (code '.$code.'). ' . curl_error($ch));
            }
        } else {
            $output = json_decode($output);
            return $output;
        }
        return false;
        curl_close($ch);
    }
    
    private function splitAddress($address) {
        if (preg_match('~(.*?)\s*(\d[\-\d]*\D*)$~', $address, $number)) {
            unset($number[0]);
            $number[0] = $number[1];
            $number[1] = $number[2];
            unset($number[2]);
            return $number;
        } else {
            return false;
        }
    }
    
}
