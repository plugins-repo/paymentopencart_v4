<?php

namespace Opencart\Catalog\Controller\Extension\Paymentopencart_v4\Payment;

/**
 * Class Paymentopencart_v4
 *
 * @package
 */
class Paymentopencart_v4 extends \Opencart\System\Engine\Controller
{
    /**
     * @return string
     */
    private $route_extension = 'extension/Paymentopencart_v4/payment/paymentopencart_v4';

    public function index(): string {
        $this->load->language($this->route_extension);

        $data['action'] = $this->url->link('extension/Paymentopencart_v4/payment/paymentopencart_v4.confirm', '', true);
        $data['merchant_id'] = $this->config->get('payment_paymentopencart_v4_merchant_id');
        $data['partner_name'] = $this->config->get('payment_paymentopencart_v4_partner_name');
        $data['merchant_redirecturl'] = $this->config->get('payment_paymentopencart_v4_merchant_redirecturl');
        $data['secret_key'] = $this->config->get('payment_paymentopencart_v4_secret_key');
        $data['test_url'] = $this->config->get('payment_paymentopencart_v4_test_url');

        return $this->load->view($this->route_extension, $data);
    }

    public function confirm(): void {
        $this->load->language($this->route_extension);
    
        $json = [];
    
        // Make sure order ID exists in session
        if (isset($this->session->data['order_id'])) {
            // Load the order model
            $this->load->model('checkout/order');
    
            $order_id = $this->session->data['order_id'];
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $amount = $order_info['total'];
    
            if ($order_info) {
                // Get merchant ID and test URL from configuration
                $merchant_id = $this->config->get('payment_paymentopencart_v4_merchant_id');
                $partner_name = $this->config->get('payment_paymentopencart_v4_partner_name');
                $merchant_redirecturl = $this->config->get('payment_paymentopencart_v4_merchant_redirecturl');
                $secret_key = $this->config->get('payment_paymentopencart_v4_secret_key');
                $test_url = $this->config->get('payment_paymentopencart_v4_test_url');

                $merchantTransactionId = $this->generateRandomString(); 

                // Prepare checksum using the correct variable names
                $checksum_maker = $merchant_id . '|' . $partner_name . '|' . $amount . '|' . $merchantTransactionId . '|' . $merchant_redirecturl. '|' . $secret_key;
                
                $checksum = md5($checksum_maker);
    
                // Prepare the data to be sent via POST
                $form_data = [
                    'amount' => $amount,
                    'currency' => $order_info['currency_code'],
                    'toid' => $merchant_id,
                    'totype' => $partner_name,
                    'merchantRedirectUrl' => $merchant_redirecturl,
                    'description' => $merchantTransactionId,
                    'checksum' => $checksum
                ];
    
                // Set the response for the form data
               
                $json['action'] = $test_url;
                $json['form'] = $form_data;

                // Optionally, you can directly redirect to the test URL here instead of returning a JSON response.
                // $this->response->redirect($test_url . '?' . http_build_query($form_data));
            } else {
                $json['error'] = $this->language->get('error_order');
            }
        } else {
            $json['error'] = $this->language->get('error_no_order');
        }
    
        // Set the response as JSON
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // Generate a random string for transaction ID
    private function generateRandomString($length = 6): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

