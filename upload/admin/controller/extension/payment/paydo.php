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
		$data['entry_methods_mode']    = $this->language->get('entry_methods_mode');
		$data['entry_hosted_page']     = $this->language->get('entry_hosted_page');
		$data['entry_project_id']      = $this->language->get('entry_project_id');
		$data['entry_jwt_token']       = $this->language->get('entry_jwt_token');
		$data['entry_enabled_methods'] = $this->language->get('entry_enabled_methods');
		$data['help_methods_mode']     = $this->language->get('help_methods_mode');
		$data['help_hosted_page']      = $this->language->get('help_hosted_page');
		$data['help_project_id']       = $this->language->get('help_project_id');
		$data['help_jwt_token']        = $this->language->get('help_jwt_token');
		$data['help_enabled_methods']  = $this->language->get('help_enabled_methods');
		$data['text_no_methods']       = $this->language->get('text_no_methods');
		$data['text_syncing']          = $this->language->get('text_syncing');
		$data['text_sync_success']     = $this->language->get('text_sync_success');
		$data['text_sync_error']       = $this->language->get('text_sync_error');
		$data['text_search_method']    = $this->language->get('text_search_method');

		$data['button_save']    = $this->language->get('button_save');
		$data['button_cancel']  = $this->language->get('button_cancel');
		$data['button_invoices'] = $this->language->get('button_invoices');
		$data['button_sync_methods'] = $this->language->get('button_sync_methods');

		$data['error_warning']    = isset($this->error['warning']) ? $this->error['warning'] : '';
		$data['error_public_id']  = isset($this->error['public_id']) ? $this->error['public_id'] : '';
		$data['error_secret_key'] = isset($this->error['secret_key']) ? $this->error['secret_key'] : '';
		$data['error_project_id'] = isset($this->error['project_id']) ? $this->error['project_id'] : '';
		$data['error_jwt_token'] = isset($this->error['jwt_token']) ? $this->error['jwt_token'] : '';
		$data['error_enabled_methods'] = isset($this->error['enabled_methods']) ? $this->error['enabled_methods'] : '';

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
		$data['invoices'] = $this->url->link('extension/payment/paydo/invoices', 'token=' . $this->session->data['token'], true);
		$data['sync_methods_url'] = str_replace('&amp;', '&', $this->url->link('extension/payment/paydo/syncmethods', 'token=' . $this->session->data['token'], true));

		$fields = array(
			'paydo_public_id',
			'paydo_secret_key',
			'paydo_order_status_wait',
			'paydo_order_status_success',
			'paydo_order_status_error',
			'paydo_status',
			'paydo_sort_order',
			'paydo_methods_mode',
			'paydo_hosted_page_enabled',
			'paydo_project_id',
			'paydo_jwt_token',
			'paydo_enabled_methods',
		);

		foreach ($fields as $f) {
			$data[$f] = isset($this->request->post[$f]) ? $this->request->post[$f] : $this->config->get($f);
		}

		if ($data['paydo_hosted_page_enabled'] === null) {
			$data['paydo_hosted_page_enabled'] = 1;
		}

		if (!is_array($data['paydo_enabled_methods'])) {
			$data['paydo_enabled_methods'] = array();
		}

		$data['paydo_available_methods'] = $this->config->get('paydo_available_methods');
		if (!is_array($data['paydo_available_methods'])) {
			$data['paydo_available_methods'] = array();
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

	public function syncmethods() {
		$this->load->language('extension/payment/paydo');
		$this->response->addHeader('Content-Type: application/json');
		if (empty($this->session->data['token']) || !isset($this->request->get['token']) ||
			!is_string($this->request->get['token']) ||
			!hash_equals((string)$this->session->data['token'], $this->request->get['token'])) {
			$this->response->setOutput(json_encode(array('success' => false, 'error' => $this->language->get('error_permission'))));
			return;
		}

		if (!$this->user->hasPermission('modify', 'extension/payment/paydo')) {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'error' => $this->language->get('error_permission'),
			)));
			return;
		}

		if (!isset($this->request->server['REQUEST_METHOD']) || $this->request->server['REQUEST_METHOD'] !== 'POST') {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'error' => $this->language->get('error_sync_request'),
			)));
			return;
		}

		$project_id = isset($this->request->post['project_id']) ? trim((string)$this->request->post['project_id']) : '';
		$jwt_token = isset($this->request->post['jwt_token']) ? trim((string)$this->request->post['jwt_token']) : '';

		if ($project_id === '' || $jwt_token === '') {
			$this->response->setOutput(json_encode(array(
				'success' => false,
				'error' => $this->language->get('error_sync_credentials'),
			)));
			return;
		}

		$result = $this->fetchAvailableMethods($project_id, $jwt_token);
		if (!$result['success']) {
			$this->response->setOutput(json_encode($result));
			return;
		}

		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('paydo');
		$settings['paydo_available_methods'] = $result['methods'];
		$this->model_setting_setting->editSetting('paydo', $settings);

		$this->response->setOutput(json_encode(array(
			'success' => true,
			'count' => count($result['methods']),
			'methods' => array_values($result['methods']),
		)));
	}

	public function invoices() {
		$this->load->language('extension/payment/paydo');
		$this->document->setTitle($this->language->get('heading_invoices'));

		$data = array();
		$data['heading_title'] = $this->language->get('heading_title');
		$data['heading_invoices'] = $this->language->get('heading_invoices');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['column_order_id'] = $this->language->get('column_order_id');
		$data['column_invoice_id'] = $this->language->get('column_invoice_id');
		$data['column_amount'] = $this->language->get('column_amount');
		$data['column_currency'] = $this->language->get('column_currency');
		$data['column_invoice_status'] = $this->language->get('column_invoice_status');
		$data['column_transaction_state'] = $this->language->get('column_transaction_state');
		$data['column_date_created'] = $this->language->get('column_date_created');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['button_back'] = $this->language->get('button_back');

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
			array(
				'text' => $this->language->get('heading_invoices'),
				'href' => $this->url->link('extension/payment/paydo/invoices', 'token=' . $this->session->data['token'], true)
			),
		);

		$data['back'] = $this->url->link('extension/payment/paydo', 'token=' . $this->session->data['token'], true);
		$data['invoice_records'] = $this->getInvoiceRecords();

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/paydo_invoices.tpl', $data));
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

		$this->request->post['paydo_methods_mode'] = !empty($this->request->post['paydo_methods_mode']) ? 1 : 0;
		$this->request->post['paydo_hosted_page_enabled'] = !empty($this->request->post['paydo_hosted_page_enabled']) ? 1 : 0;
		$this->request->post['paydo_project_id'] = isset($this->request->post['paydo_project_id'])
			? trim((string)$this->request->post['paydo_project_id'])
			: '';
		$this->request->post['paydo_jwt_token'] = isset($this->request->post['paydo_jwt_token'])
			? trim((string)$this->request->post['paydo_jwt_token'])
			: '';

		$available_methods = $this->config->get('paydo_available_methods');
		if (!is_array($available_methods)) {
			$available_methods = array();
		}
		$this->request->post['paydo_available_methods'] = $available_methods;

		$enabled_methods = isset($this->request->post['paydo_enabled_methods']) && is_array($this->request->post['paydo_enabled_methods'])
			? $this->request->post['paydo_enabled_methods']
			: array();
		$clean_enabled_methods = array();
		foreach ($enabled_methods as $enabled_method) {
			if (is_scalar($enabled_method)) {
				$clean_enabled_methods[] = trim((string)$enabled_method);
			}
		}
		$enabled_methods = array_values(array_unique($clean_enabled_methods));
		$enabled_methods = array_values(array_intersect($enabled_methods, array_map('strval', array_keys($available_methods))));
		$this->request->post['paydo_enabled_methods'] = $enabled_methods;

		if ($this->request->post['paydo_methods_mode']) {
			if (!$this->request->post['paydo_hosted_page_enabled'] && !$enabled_methods) {
				$this->error['enabled_methods'] = $this->language->get('error_enabled_methods');
			}
		}

		return !$this->error;
	}

	private function fetchAvailableMethods($project_id, $jwt_token) {
		$url = 'https://api.paydo.com/v3/instrument-settings/payment-methods/available-for-application/' . rawurlencode($project_id);
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Authorization: Bearer ' . $jwt_token,
		));

		$response = curl_exec($curl);
		$http_code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$curl_error = curl_error($curl);
		curl_close($curl);

		if ($response === false) {
			return array(
				'success' => false,
				'error' => $curl_error !== '' ? $curl_error : $this->language->get('error_sync_request'),
			);
		}

		$json = json_decode($response, true);
		if ($http_code !== 200 || !is_array($json)) {
			return array(
				'success' => false,
				'error' => sprintf($this->language->get('error_sync_api'), $http_code),
			);
		}

		if (!isset($json['data']) || !is_array($json['data'])) {
			return array('success' => false, 'error' => sprintf($this->language->get('error_sync_api'), $http_code));
		}
		$rows = $json['data'];
		$methods = array();

		foreach ($rows as $row) {
			if (!is_array($row)) {
				continue;
			}

			$method = isset($row['paymentMethod']) && is_array($row['paymentMethod']) ? $row['paymentMethod'] : $row;
			if (empty($method['isEnabled']) || !isset($method['identifier'], $method['title'])) {
				continue;
			}
			if (!is_scalar($method['identifier']) || !is_string($method['title'])) continue;

			$identifier = trim((string)$method['identifier']);
			$title = trim((string)$method['title']);
			if (!preg_match('/^[1-9][0-9]*$/', $identifier) || $title === '') {
				continue;
			}

			$logo = isset($method['logo']) ? trim((string)$method['logo']) : '';
			if ($logo !== '' && !$this->isSafeRemoteUrl($logo)) {
				$logo = '';
			}

			$methods[$identifier] = array(
				'identifier' => $identifier,
				'title' => $title,
				'logo' => $logo,
			);
		}

		uasort($methods, array($this, 'compareMethodsByTitle'));

		return array(
			'success' => true,
			'methods' => $methods,
		);
	}

	private function isSafeRemoteUrl($url) {
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			return false;
		}

		$scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
		return in_array($scheme, array('http', 'https'), true);
	}

	private function compareMethodsByTitle($left, $right) {
		$left_title = isset($left['title']) ? (string)$left['title'] : '';
		$right_title = isset($right['title']) ? (string)$right['title'] : '';
		return strnatcasecmp($left_title, $right_title);
	}

	private function ensureInvoiceTableExists() {
		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paydo_invoice` (
				`paydo_invoice_id` INT(11) NOT NULL AUTO_INCREMENT,
				`order_id` INT(11) NOT NULL,
				`invoice_id` VARCHAR(64) NOT NULL,
				`txid` VARCHAR(64) NOT NULL DEFAULT '',
				`amount` DECIMAL(15,4) NOT NULL DEFAULT '0.0000',
				`currency` VARCHAR(8) NOT NULL DEFAULT '',
				`invoice_status` INT(11) DEFAULT NULL,
				`transaction_state` INT(11) DEFAULT NULL,
				`date_created` DATETIME NOT NULL,
				`date_modified` DATETIME NOT NULL,
				PRIMARY KEY (`paydo_invoice_id`),
				UNIQUE KEY `paydo_order_id` (`order_id`),
				UNIQUE KEY `paydo_invoice_identifier` (`invoice_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8"
		);
	}

	private function getInvoiceRecords() {
		$this->ensureInvoiceTableExists();

		$query = $this->db->query(
			"SELECT order_id, invoice_id, amount, currency, invoice_status, transaction_state, date_created, date_modified
			 FROM `" . DB_PREFIX . "paydo_invoice`
			 ORDER BY date_modified DESC
			 LIMIT 100"
		);

		return isset($query->rows) && is_array($query->rows) ? $query->rows : array();
	}
}
