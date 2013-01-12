<?php

class Hackathon_Dataprovider_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction() 
	{
		$fieldsNeeded = array(
			// only get the fields we need from DP's response
			'company',
			'emailaddresses',
			'zipcode',
			'country',
			'city',
			'region',
			'address',
			'phonenumber',
			'faxnumber',
			'cocnumber'
		);
		
		$mapping = array(
			// dataprovider name => magento field name
			'country' => 'country_id',
			'faxnumber' => 'fax',
			'cocnumber' => 'kvknummer',
			'zipcode' => 'postcode',
			'phonenumber' => 'telephone',
			'taxvat' => 'tax',
		);
		
		$helper = Mage::helper('dataprovider');
		
		$params = $this->getRequest()->getParams();
		foreach($params as $field=>$value) {
			switch($field) {
				case 'postcode':
					$DPdata = $helper->zipcode($value);
					break;
				case 'tax':
					$DPdata = $helper->tax($value);
					break;
				case 'coc':
					$DPdata = $helper->chamberofcommerce($value);
					break;
				case 'phone':
					$DPdata = $helper->phone($value);
					break;
				case 'email':
					// get hostname from email
					if(stripos($value,'@')!==false) {
						$value = substr($value,(stripos($value,'@')+1));
					}
					$DPdata = $helper->hostname($value);
					break;
			}
		}
		
		if(isset($DPdata)) {
			$dataObject = array_pop($DPdata->data);

			$data = array();
			foreach($dataObject as $key=>$value) {
				if(in_array($key,$fieldsNeeded)) {
					if($key=='emailaddresses') {
						$emailaddresses = explode(',',$value);
						$data['email'] = array_pop($emailaddresses);
					} else {
						$mapped = (isset($mapping[$key]) ? $mapping[$key] : $key);
						$data[$mapped] = $value;
					}
				}
			}
	
			echo json_encode($data);
		} else {
			echo json_encode(array());
		}
		
	}
}


