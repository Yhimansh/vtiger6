<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class HelpDesk_Edit_View extends Vtiger_Edit_View {

	public function process(Vtiger_Request $request) {
        global $log, $adb;

		$moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
		$recordId = $request->get('record');
        $recordModel = $this->record;
        if(!$recordModel){
           if (!empty($recordId)) {
               $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
               // $relatedProducts = getAssociatedProducts($moduleName, $recordId);

               $relatedProducts = $this->getProducts($moduleName, $recordId);

               //echo '<pre>'; print_r($relatedProducts);die;

                $viewer->assign('PRODUCTS', $relatedProducts);
                $viewer->assign('RECORD_ID', $record);
                $viewer->assign('MODE', 'edit');
           } else {
               $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
               $viewer->assign('MODE', '');
           }
            $this->record = $recordModel;
        }

		

		// if(!$this->record){
  //           $this->record = $recordModel;
  //       }

		parent::process($request);
	}


    function getProducts($moduleName, $recordId) {
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
        $params = array($recordId);

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
        return $product_Detail;
    }

}
