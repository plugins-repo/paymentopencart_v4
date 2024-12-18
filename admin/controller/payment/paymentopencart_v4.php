<?php

namespace Opencart\Admin\Controller\Extension\Paymentopencart_v4\Payment;
/**
 * Class Paymentopencart_v4
 *
 * @package Opencart\Admin\Controller\Extension\Paymentopencart_v4\Payment
 */
class Paymentopencart_v4 extends \Opencart\System\Engine\Controller
{
    /**
	 * @return void
	 */
    private $error = [];
    private $route_extension = 'extension/Paymentopencart_v4/payment/paymentopencart_v4';


    public function index(): void
    {
        
        $this->load->language($this->route_extension);
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/opencart/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->route_extension, 'user_token=' . $this->session->data['user_token'])
        ];

        $data['heading_title'] = $this->language->get('heading_title');
        $data['save'] = $this->url->link('extension/Paymentopencart_v4/payment/paymentopencart_v4.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
        $data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
        $data['entry_test_url'] = $this->language->get('entry_test_url');

        $data['merchant_id'] = $this->config->get('payment_paymentopencart_v4_merchant_id');
        $data['partner_name'] = $this->config->get('payment_paymentopencart_v4_partner_name');
        $data['merchant_redirecturl'] = $this->config->get('payment_paymentopencart_v4_merchant_redirecturl');
        $data['secret_key'] = $this->config->get('payment_paymentopencart_v4_secret_key');
        $data['test_url'] = $this->config->get('payment_paymentopencart_v4_test_url');

        $data['payment_paymentopencart_v4_status'] = $this->config->get('payment_paymentopencart_v4_status');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
           save(); 
        }

        $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->route_extension, $data));
    }
    /**
	 * @return void
	 */
    public function save(): void {
		$this->load->language($this->route_extension);

		$json = [];

		if (!$this->user->hasPermission('modify', $this->route_extension)) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('payment_paymentopencart_v4', $this->request->post);
            $json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


    public function send() {

        $order_id = $this->session->data['order_id'];
        $merchant_id = $this->config->get('payment_paymentopencart_v4_merchant_id');
        $partner_name = $this->config->get('payment_paymentopencart_v4_partner_name');
        $merchant_redirecturl = $this->config->get('payment_paymentopencart_v4_merchant_redirecturl');
        $secret_key = $this->config->get('payment_paymentopencart_v4_secret_key');
        $test_url = $this->config->get('payment_paymentopencart_v4_test_url');
        $amount = $order_info['total'];

        // Prepare order details
        $order_info = $this->model_checkout_order->getOrder($order_id);

        // Send order details to the test URL
        $data = [
            'toid' => $merchant_id,
            'totype' => $partner_name,
            'merchantRedirectUrl' => $merchanttransactionid,
            'order_id' => $order_id,
            'amount' => $amount,
            'currency' => $order_info['currency'],
        ];

        // Redirect to the test URL
        $this->response->redirect($test_url . '?' . http_build_query($data));
    }

 
}

