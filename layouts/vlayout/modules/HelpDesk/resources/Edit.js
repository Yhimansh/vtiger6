/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Edit_Js("HelpDesk_Edit_Js",{},{
	
	
	fetchItems : function(container){

		$("#productcategory").on("change",function(){
			// alert($("#productcategory").val());

			var subProrductParams = {
			'module' : "HelpDesk",
			'action' : "GetProductDetails",
			'category_name' : $("#productcategory").val()
			}
			var progressInstace = jQuery.progressIndicator();
			AppConnector.request(subProrductParams).then(
				function(data){
					console.log(data);
					var responseData = data.result;
					//var subProductsContainer = jQuery('#pro_div',lineItemRow);
					// var subProductIdHolder = jQuery('.subProductIds',lineItemRow);

					var subProductHtml = '';
					for(var info in responseData) {
						// console.log(responseData[info].productname);
						subProductHtml += '<tr>'+
						            '<td colspan="2">'+
						            '<input type="hidden" name="item[product][]" id="item1" value="'+responseData[info].productname +'" >'+
						            responseData[info].productname +' (Qty In stock : <b>'+parseInt(responseData[info].qtyinstock)+' </b>)</td>'+
						            '<td colspan="2"><input type="hidden" name="item[qtystck][]"  class="qtystock" value="'+parseInt(responseData[info].qtyinstock)+'" ><input type="text" name="item[qty][]" class="qty" value=""></td>'+
						            '<td colspan="2"><input type="hidden" name="item[listprice][]" id="listprice1" value="'+parseFloat(responseData[info].unit_price).toFixed(2)+'" ></td>'+
						        '</tr>';
					}
					//subProductIdHolder.val(Object.keys(responseData).join(':'));
					$(".pro_div").html(subProductHtml);
					//subProductsContainer.html(subProductHtml);
					progressInstace.hide();
				},
				function(error,err){
					//TODO : handle the error case
				}
			);
		})

	},
	
	stockValidate : function(container){
		$(document).on("focusout",".qty",function(){
			var c_qty = $(this).val();
			var stck = $(this).siblings(".qtystock").val();
			console.log(c_qty);
			console.log(stck);
			if(c_qty > stck) {
				alert("stock is low!!!");
				$(this).val("");
			}
		})
	},

	registerBasicEvents : function(container){
		this._super(container);
		this.fetchItems(container);
		this.stockValidate();
	}
})