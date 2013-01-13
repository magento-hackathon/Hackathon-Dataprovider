<?php

class Hackathon_Dataprovider_IndexController extends Mage_Core_Controller_Front_Action
{
	/*
	 * @TODO Make a modal popup when multiple results are returned by Dataprovider
	 * @TODO Request an API call to be made by Dataprovider to search on company name
	 * @TODO Append company data to the contact form, based on email adddress (hostname call)
	 * @TODO OneStepCheckout, CheckItOut, etc support
	 * @DONE Decide on how to handle the insertion of data when data already exists in input fields
	 */
	
	public function indexAction() 
	{	
		$fieldsNeeded = array(
			// only get the fields we need from DP's response
			'company',
			'emailaddresses',
			'address',
			'zipcode',
			'city',
			'region',
			'country',
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
			'address' => 'street1',
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
			$json = array();
			foreach($DPdata->data as $dataObject) {
				$orderedData = $data = array();
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
				
				if(!isset($data['company']) AND !empty($dataObject->domain)) $data['company'] = $dataObject->domain;
				// order the data nicely for the preview functionality
				foreach($fieldsNeeded as $key) {
					$mapped = (isset($mapping[$key]) ? $mapping[$key] : $key);
					if(isset($data[$mapped])) {
						$orderedData[$mapped] = $data[$mapped];
					}
				}
				
				$json[] = $orderedData;
			}
			echo json_encode($json);
		} else {
			echo json_encode(array());
		}
		
	}
}


