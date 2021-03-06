<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class HelpDesk_Detail_View extends Vtiger_Detail_View {
	
	function __construct() {
		parent::__construct();
		$this->exposeMethod('showRelatedRecords');
	}

	function showModuleDetailView(Vtiger_Request $request) {
		$products = $this->getProducts($request);
		echo parent::showModuleDetailView($request);
		//$viewer = $this->getViewer($request);
		//$viewer->assign('PRODUCTS', $products);
		
	}

	function getProducts(Vtiger_Request $request) {
		$record = $request->get('record');
		$moduleName = $request->getModule();

		global $log, $adb;
        $query="SELECT
            case when vtiger_products.productid != '' then vtiger_products.productname else vtiger_service.servicename end as productname,
            case when vtiger_products.productid != '' then vtiger_products.product_no else vtiger_service.service_no end as productcode,
            case when vtiger_products.productid != '' then vtiger_products.unit_price else vtiger_service.unit_price end as unit_price,
            case when vtiger_products.productid != '' then vtiger_products.qtyinstock else 'NA' end as qtyinstock,
            case when vtiger_products.productid != '' then 'Products' else 'Services' end as entitytype,
                        vtiger_inventoryproductrel.listprice,
                        vtiger_inventoryproductrel.description AS product_description,
                        vtiger_inventoryproductrel.*,vtiger_crmentity.deleted
                        FROM vtiger_inventoryproductrel
                        LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_inventoryproductrel.productid
                        LEFT JOIN vtiger_products
                                ON vtiger_products.productid=vtiger_inventoryproductrel.productid
                        LEFT JOIN vtiger_service
                                ON vtiger_service.serviceid=vtiger_inventoryproductrel.productid
                        WHERE id=?
                        ORDER BY sequence_no";
        $params = array($record);

        $result = $adb->pquery($query, $params);
        $num_rows=$adb->num_rows($result);

        for($i=1;$i<=$num_rows;$i++)
        {
            $deleted = $adb->query_result($result,$i-1,'deleted');
            $hdnProductId = $adb->query_result($result,$i-1,'productid');
            $hdnProductcode = $adb->query_result($result,$i-1,'productcode');
            $productname=$adb->query_result($result,$i-1,'productname');
            $qtyinstock=$adb->query_result($result,$i-1,'qtyinstock');
            $qty=$adb->query_result($result,$i-1,'quantity');
            $unitprice=$adb->query_result($result,$i-1,'unit_price');
            $listprice=$adb->query_result($result,$i-1,'listprice');
        
            // if(($deleted) || (!isset($deleted))){
            //     $product_Detail[$i]['productDeleted'] = true;
            // }elseif(!$deleted){
            //     $product_Detail[$i]['productDeleted'] = false;
            // }

            if($listprice == '')
                $listprice = $unitprice;
            if($qty =='')
                $qty = 1;
            $product_Detail[$i]['hdnProductId'] = $hdnProductId;
            $product_Detail[$i]['qty']=decimalFormat($qty);
            $product_Detail[$i]['listPrice']=$listprice;
            $product_Detail[$i]['unitPrice']=number_format($unitprice, $no_of_decimal_places,'.','');
            $product_Detail[$i]['qtyInStock']=decimalFormat((int)$qtyinstock + (int)$qty);
            $product_Detail[$i]['productName']= from_html($productname);
        }
        //echo '<pre>'; print_r($product_Detail);die;
        $viewer = $this->getViewer($request);
		$viewer->assign('PRODUCTS', $product_Detail);
        return true;
	}

	/**
	 * Function to get activities
	 * @param Vtiger_Request $request
	 * @return <List of activity models>
	 */
	public function getActivities(Vtiger_Request $request) {
		$moduleName = 'Calendar';
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		if($currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
			$moduleName = $request->getModule();
			$recordId = $request->get('record');

			$pageNumber = $request->get('page');
			if(empty ($pageNumber)) {
				$pageNumber = 1;
			}
			$pagingModel = new Vtiger_Paging_Model();
			$pagingModel->set('page', $pageNumber);
			$pagingModel->set('limit', 10);

			if(!$this->record) {
				$this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
			}
			$recordModel = $this->record->getRecord();
			$moduleModel = $recordModel->getModule();

			$relatedActivities = $moduleModel->getCalendarActivities('', $pagingModel, 'all', $recordId);

			$viewer = $this->getViewer($request);
			$viewer->assign('RECORD', $recordModel);
			$viewer->assign('MODULE_NAME', $moduleName);
			$viewer->assign('PAGING_MODEL', $pagingModel);
			$viewer->assign('PAGE_NUMBER', $pageNumber);
			$viewer->assign('ACTIVITIES', $relatedActivities);

			return $viewer->view('RelatedActivities.tpl', $moduleName, true);
		}
	}
}
