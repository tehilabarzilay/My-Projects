<?php

/**
 * Plugin Name: xxx Invoice
 * Plugin URI: https://xxx.co.il/
 * Description: plugin for createting Invoice-Receipts in xxx system.
 * Version: 1.5
 * Author: xxx
 * Author URI: https://xxx.co.il/
 */

defined('ABSPATH') or die('No script kiddies please!');

add_action('admin_menu', 'xxx_Invoice_Receipt_create_menu');
function xxx_Invoice_Receipt_create_menu(){
	add_menu_page('xxx Invoice Settings', 'xxx הגדרות', 'administrator', __FILE__, 'xxx_Invoice_Receipt_settings_page');
	add_action('admin_init', 'register_xxx_Invoice_Receipt_settings');
}

function register_xxx_Invoice_Receipt_settings(){
	//register our settings
	register_setting('xxx-Invoice-Receipt-settings-group', 'Api_Key');
	register_setting('xxx-Invoice-Receipt-settings-group', 'user_name');
	register_setting('xxx-Invoice-Receipt-settings-group', 'list_cc');
	register_setting('xxx-Invoice-Receipt-settings-group', 'list_paypal');
	register_setting('xxx-Invoice-Receipt-settings-group', 'list_cod_action');
}

function xxx_Invoice_Receipt_settings_page(){
?>
	<div class="wrap">
		<h1>הגדרות לתוסף xxx:</h1>

		<body><br>
			לקוח יקר, <br>
			התוסף מפיק חשבוניות מס קבלה דרך מערכת ... ורלוונטי בעת קבלת תשלום באשראי או פייפאל בלבד לכן תחילה יש להשלים את הגדרות קבלת התשלום בחנות.<br>
			כדי לקבל את השדות הנדרשים להפעלת התוסף, <br>
			יש להיכנס לכתובת <a href="https://xxx.co.il/app/companies/company_api_settings">https://xxx.co.il/app/companies/company_api_settings</a> <br>
			יש לשים לב בהגדרות הממשקים ב... כי "מצב בדיקות" לא פעיל.
			<br />
		</body>
		<form method="post" action="options.php">
			<?php settings_fields('xxx-Invoice-Receipt-settings-group'); ?>
			<?php do_settings_sections('xxx-Invoice-Receipt-settings-group'); ?>
			<br />
			<table class="form-table2">
				<tr valign="top">
					<td><b>Api Key:</b><br><input style="width:280px;" type="text" name="Api_Key" value="<?php echo esc_attr(get_option('Api_Key')); ?>" /></td>
					<td style="padding-right: 20px;"><b>שם משתמש:</b><br><input style="width:280px;" type="text" name="user_name" value="<?php echo esc_attr(get_option('user_name')); ?>" /></td>
				</tr>
			</table>
			<br />
			<table class="form-table1">
				<tr valign="top">

					<?php
					$gateways = WC()->payment_gateways->get_available_payment_gateways();
					$enabled_gateways = [];
					if ($gateways) {
						foreach ($gateways as $gateway) {
							if ($gateway->enabled == 'yes') {
								$enabled_gateways[$gateway->id] = $gateway->method_title;		
							}
						}
					}
					echo "<td  style='width:300px;'><b>בחר את שיטת התשלום באשראי: 	<b/>";
					echo "<select id='cc' name='list_cc'  style='font-weight:normal; width:280px;' >
						<option value='no'>אין תשלום בכרטיס אשראי</option>";
					foreach ($enabled_gateways as $get_cc => $title) {					
						if ($get_cc == get_option('list_cc')) {
							echo '<option value="' . $get_cc. '" selected name="' . $get_cc . '">' . $title . '</option>';
						} else {
							echo '<option value="' . $get_cc . '" name="' . $get_cc . '" >' . $title . '</option>';
						}
					}
					echo '</select>  </td>';

					echo "<td style='width:300px;'><b>בחר את שיטת התשלום בפייפאל: 	<b/>";
					echo "<select id='paypal' name='list_paypal' style='font-weight:normal; width:280px;'>
						<option value='no' >אין תשלום בפייפאל</option>";
					foreach ($enabled_gateways as $get_paypal => $title) {					
						if ($get_paypal == get_option('list_paypal')) {
							echo '<option value="' . $get_paypal. '" selected name="' . $get_paypal . '">' . $title . '</option>';
						} else {
							echo '<option value="' . $get_paypal . '" name="' . $get_paypal . '" >' . $title . '</option>';
						}
					}				
					echo '</select> </td>';
					
					//הוספת אפשרויות פעולה במצב של תשלום במזומן/המחאה
					$selected_no = get_option('list_cod_action') == 'no' ? ' selected' : '';
					$selected_proforma = get_option('list_cod_action') == 'Proforma' ? ' selected' : '';
					$selected_invoice = get_option('list_cod_action') == 'Invoice' ? ' selected' : '';
					echo "<td  style='width:300px;'><b>בחר פעולה לביצוע בעת שיטת תשלום במזומן / בהמחאה:	<b/>";
					echo "<select id='cod_action' name='list_cod_action'  style='font-weight:normal; width:280px;' >
						<option value='no'{$selected_no}>ללא ביצוע פעולה</option>
						<option value='Proforma' name='Proforma'{$selected_proforma}>הנפקת ח-ן עסקה</option>
						<option value='Invoice' name='Invoice'{$selected_invoice}>הנפקת חשבונית מס</option>";
					echo '</select>  </td>';

					?>

				</tr>
			</table>

			<?php submit_button();	?>

		</form>
	</div>
<?php
}

//יצירת קבלה בעת שתלום שעבר בהצלחה
add_action('woocommerce_order_status_changed', 'send_api');


function send_api($order_id){
	$available_gatewayz = WC()->payment_gateways->get_available_payment_gateways();
	$api_key = esc_attr(get_option('Api_Key'));
	$api_user = esc_attr(get_option('user_name'));
	if ((!empty($api_key)) && (!empty($api_user))) {
		if (!$order_id)
			return;
		
		$order = wc_get_order($order_id);		
		
		// Get the meta data in an unprotected array
		$order_data = $order->get_data();
		// Encoding in json
		$json_order_data = json_encode($order_data);
		
		$payment_method = $order->get_payment_method();
		if ( $order_data["status"] != "processing" && !($order_data["status"] == "on-hold" && $payment_method == 'cheque') ) {
			return;
		}


		$order_key = $order->get_order_number();
		$subject = "מס' הזמנה: " . $order_key;
		$payment_title = $order->get_payment_method_title();

		$payment_type = "x";
		if ( strtoupper(get_option('list_cc')) == strtoupper($payment_method)) {
			$payment_type = "Cc";
		}
		if (strtoupper(get_option('list_paypal')) == strtoupper($payment_method)) {
			$payment_type = "Paypal";
		}

		if (strtoupper('cod') == strtoupper($payment_method) || strtoupper('cheque') == strtoupper($payment_method)) {
			$payment_type = "cod_cheque";
		}

		if ($payment_type != "x") {

			$billing_email = $order->get_billing_email();
			$customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
			$all_address = $order->get_address();
		
			$country = $all_address["country"];
			$city = $all_address["city"];
			$address = $all_address["address_1"] . " " . $all_address["address_2"];
			$zip_code = $all_address["postcode"];
		
			// פרטים על המוצר
			$total = 0;
			foreach ($order->get_items() as $item_id => $item) {
				$product_price = $item->get_total();
				$product_quantity = $item->get_quantity();
				//מחיר ליחידה
				$unit_price = $product_price / $product_quantity;
				$catalog_id = null;
				$product = wc_get_product( $item->get_product_id() );
				if($product){
					$catalog_id = $product->get_sku();
				}
				$invoice_lines[] = [
					'description' => $item->get_name(),
					'quantity' => $item->get_quantity(),
					'price_per_unit' => $unit_price,
					'include_vat' => 'true',
					'catalog_id' => ((is_null($catalog_id)) ? "" : $catalog_id),
				];
				$amount = $item->get_total();
				$total += $item->get_total();
			}
			
			$shipping_total = $order->get_shipping_total() ? $order->get_shipping_total() : 0;

			if ( $shipping_total > 0){
				foreach( $order->get_items( 'shipping' ) as $item_id => $item ){
					$order_item_name             = $item->get_name();			
					$shipping_method_instance_id = $item->get_instance_id(); 
					$shipping_method_total       = $item->get_total();
					$shipping_method_total_tax   = $item->get_total_tax();
					$shipping_method_taxes       = $item->get_taxes();
					
					$invoice_lines[] = [					
						'description' => 'דמי משלוח',
						'quantity' => 1,
						'price_per_unit' => $shipping_method_total,
						'include_vat' => 'true',
						'catalog_id' => $shipping_method_instance_id,
					];
				}
				$total += $shipping_total;
			}
			$customer_mobile = $order->get_billing_phone();

			$date_order = date("d-m-Y", strtotime($order->get_date_created()));

			$ch = curl_init();

			//בדיקת שיטת התשלום וקריאה לא.פ.אי המתאים (הנפקת חשבונית מס קבלה/ח-ן עסקה/חשבונית)
			if ($payment_type == "Paypal" || $payment_type == "Cc"){
				curl_setopt($ch, CURLOPT_URL, "xxx/generate_invoice_receipt");
			} elseif (strtoupper(get_option('list_cod_action')) == strtoupper("Proforma")) {
				curl_setopt($ch, CURLOPT_URL, "xxx/generate_proforma");
			} elseif (strtoupper(get_option('list_cod_action')) == strtoupper("Invoice")) {
				curl_setopt($ch, CURLOPT_URL, "xxx/generate_invoice");
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			$invoice_lines1 = json_encode($invoice_lines);			
			$receipt_lines_data[] = [
				'payment_type' => $payment_type,
				'date' => $date_order,
				'amount' => $total,
			];
			$receipt_lines = json_encode($receipt_lines_data);
			$data = array(
				"api_user" => $api_user, "woocommerce" => "1", "casual_customer" => "1", "customer_mail" => $billing_email, "customer_name" => $customer_name, "customer_mobile" => $customer_mobile,
				"customer_business_phone" => "", "customer_city" => $city, "customer_address" => $address, "customer_zip_code" => $zip_code, "document_subject" => $subject, "document_remarks" => "", "document_lang" => "hb",
				"document_no_vat" => "false", "discount" => "false", "document_rounded" => "false", "send_by_mail" => "true", 
				"invoice_lines" => $invoice_lines, "receipt_lines" => $receipt_lines_data, "order_id" => $order_key, "order_data" => $order_data
			);

			$postdata = json_encode($data);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Accept: application/json",
				"Content-Type: application/json",
				"api_key: $api_key"
			));

			$response = curl_exec($ch);
			curl_close($ch);
		}
	}
}

?>