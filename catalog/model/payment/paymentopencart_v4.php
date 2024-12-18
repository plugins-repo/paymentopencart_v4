<?php
namespace Opencart\Catalog\Model\Extension\Paymentopencart_v4\Payment;
/**
 * Class Paymentopencart_v4
 *
 * @package
 */
class Paymentopencart_v4 extends \Opencart\System\Engine\Model 
{
    /**
	 * @param array $address
	 *
	 * @return array
	 */
    private $route_extension = 'extension/Paymentopencart_v4/payment/paymentopencart_v4';

    public function getMethods(array $address = []): array {
        $this->load->language($this->route_extension);

        // Fetch merchant_id and test_url from the config
        $merchant_id = $this->config->get('payment_paymentopencart_v4_merchant_id');
        $test_url = $this->config->get('payment_paymentopencart_v4_test_url');

        // Determine payment status
        if ($this->cart->hasSubscription()) {
            $status = false;
        } elseif (!$this->cart->hasShipping()) {
            $status = false;
        } elseif (!$this->config->get('config_checkout_payment_address')) {
            $status = true;
        } elseif (!$this->config->get('payment_paymentopencart_v4_geo_zone_id')) {
            $status = true;
        } else {
            // Check if the address fits the geo zone settings
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_paymentopencart_v4_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");

            $status = $query->num_rows ? true : false;
        }

        $method_data = [];

        if ($status) {
            // Prepare method data, including merchant ID and URL for display or further processing
            $option_data['paymentopencart_v4'] = [
                'code' => 'paymentopencart_v4.paymentopencart_v4',
                'name' => $this->language->get('heading_title'),
            ];

            $method_data = [
                'code'       => 'paymentopencart_v4',
                'name'       => $this->language->get('heading_title'),
                'option'     => $option_data,
                'sort_order' => $this->config->get('payment_paymentopencart_v4_sort_order')
            ];
        }

        return $method_data;
    }

    // Function to send data to the payment gateway using merchant_id and test_url
    public function sendPayment($order_id) {
                $merchant_id = $this->config->get('payment_paymentopencart_v4_merchant_id');
                $partner_name = $this->config->get('payment_paymentopencart_v4_partner_name');
                $merchant_redirecturl = $this->config->get('payment_paymentopencart_v4_merchant_redirecturl');
                $secret_key = $this->config->get('payment_paymentopencart_v4_secret_key');
                $test_url = $this->config->get('payment_paymentopencart_v4_test_url');

        // Fetch order details
        $order_info = $this->model_checkout_order->getOrder($order_id);

        // Prepare data to send
        $data = [
                    'toid' => $merchant_id,
                    'totype' => $partner_name,
                    'merchantRedirectUrl' => $merchanttransactionid,
                    'order_id' => $order_id,
                    'amount' => $amount,
                    'currency' => $order_info['currency'],
            // Add more data as needed
        ];

        // Send data to the test URL (you can modify this to use cURL or any other method as required)
        $response = $this->sendRequest($test_url, $data);

        return $response;
    }

    // Function to send request to the payment gateway
    private function sendRequest($url, $data) {
        // You can implement a cURL or other HTTP request mechanism here
        // For example, using cURL:
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}

