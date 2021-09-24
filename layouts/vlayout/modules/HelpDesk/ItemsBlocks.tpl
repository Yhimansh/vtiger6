
{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}

    <table class="table table-bordered blockContainer lineItemTable" id="lineItemTab">
        <tr>
            <th colspan="2"><span class="inventoryLineItemHeader">{vtranslate('LBL_ITEM_DETAILS', $MODULE)}</span></th>
            <th colspan="2" class="chznDropDown">
                <div class="row-fluid">
                   
                </div>
            </th>
            <th colspan="2" class="chznDropDown">
                <div class="row-fluid">
                    <div class="inventoryLineItemHeader">
                        
                    </div>
                    
                </div>
            </th>
        </tr>
        <tr>
            <td colspan="2"><input type="text" name="item[product][1]" id="item1" value="205" ></td>
            <td colspan="2"><input type="text" name="item[qty][1]" id="qty1" value="1" ></td>
            <td colspan="2"><input type="hidden" name="item[listprice][1]" id="listprice1" value="10" ></td>
        </tr>
         <tr>
            <td colspan="2"><input type="text" name="item[product][2]" id="item2" value="199"></td>
            <td colspan="2"><input type="text" name="item[qty][2]" id="qty2" value="2" ></td>
            <td colspan="2"><input type="hidden" name="item[listprice][2]" id="listprice2" value="20" ></td>
        </tr>
            <tr id="row" class="lineItemRow" data-quantity-in-stock=""></tr>
            <tr id="row1" class="lineItemRow"></tr>
    </table>


{/strip}