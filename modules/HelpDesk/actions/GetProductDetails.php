<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_GetProductDetails_Action extends Vtiger_Action_Controller {

	public function checkPermission(Vtiger_Request $request) {
		
		  }

	public function process(Vtiger_Request $request) {
		$db = PearDatabase::getInstance();
		
		$params = array();
		$field = array();
		$params[] = $request->get('category_name');
		//echo '<pre>';print_r($request);die();

		$query = "Select vtiger_products.* from vtiger_products 
		 inner join vtiger_crmentity on vtiger_products.productid=vtiger_crmentity.crmid where vtiger_crmentity.deleted=0 and vtiger_products.productcategory=?";

		$result = $db->pquery($query, $params);
		$noOfRows = $db->num_rows($result);
		$i = 0;
		while($row=$db->fetchByAssoc($result)) {
		    $field[$i]=$row;
		    $i++;
		}
		//echo '<pre>';print_r($field);die();
		$response = new Vtiger_Response();
		$response->setResult($field);
		$response->emit();

	}


	
}
