<?php
class ControllerExtensionPaymentPaydo extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/paydo');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('paydo', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect(
				$this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
			);
		}

		$data = array();

		$data['heading_title']   = $this->language->get('heading_title');
		$data['text_edit']       = $this->language->get('text_edit');
		$data['text_enabled']    = $this->language->get('text_enabled');
		$data['text_disabled']   = $this->language->get('text_disabled');

		$data['entry_public']          = $this->language->get('entry_public');
		$data['entry_secret']          = $this->language->get('entry_secret');
		$data['entry_complete_status'] = $this->language->get('entry_complete_status');
		$data['entry_pending_status']  = $this->language->get('entry_pending_status');
		$data['entry_failed_status']   = $this->language->get('entry_failed_status');
		$data['entry_status']          = $this->language->get('entry_status');
		$data['entry_sort_order']      = $this->language->get('entry_sort_order');

		$data['button_save']    = $this->language->get('button_save');
		$data['button_cancel']  = $this->language->get('button_cancel');

		$data['error_warning']    = isset($this->error['warning']) ? $this->error['warning'] : '';
		$data['error_public_id']  = isset($this->error['public_id']) ? $this->error['public_id'] : '';
		$data['error_secret_key'] = isset($this->error['secret_key']) ? $this->error['secret_key'] : '';

		$data['breadcrumbs'] = array(
			array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
			),
			array(
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
			),
			array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/payment/paydo', 'token=' . $this->session->data['token'], true)
			),
		);

		$data['action'] = $this->url->link('extension/payment/paydo', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

		$fields = array(
			'paydo_public_id',
			'paydo_secret_key',
			'paydo_order_status_wait',
			'paydo_order_status_success',
			'paydo_order_status_error',
			'paydo_status',
			'paydo_sort_order',
		);

		foreach ($fields as $f) {
			$data[$f] = isset($this->request->post[$f]) ? $this->request->post[$f] : $this->config->get($f);
		}

		$catalog_url = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;
		$data['ipn_url'] = $catalog_url . 'index.php?route=extension/payment/paydo/callback';

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/paydo.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/paydo')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['paydo_public_id'])) {
			$this->error['public_id'] = $this->language->get('error_public_id');
		}

		if (empty($this->request->post['paydo_secret_key'])) {
			$this->error['secret_key'] = $this->language->get('error_secret_key');
		}

		return !$this->error;
	}
}