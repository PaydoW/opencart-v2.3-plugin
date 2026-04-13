<?php
class ControllerExtensionPaymentPaydo extends Controller {
	/** @var resource|null */
	private $curl = null;

	public function index() {
		$this->load->language('extension/payment/paydo');

		$data = array(
			'button_pay' => $this->language->get('button_pay'),
			'paydo_url'  => $this->url->link('extension/payment/paydo/pay', '', true),
		);

		return $this->load->view('extension/payment/paydo.tpl', $data);
	}

	public function pay() {
		$this->response->addHeader('Content-Type: application/json');

		if (empty($this->session->data['order_id'])) {
			$this->response->setOutput(json_encode(array('error' => 'Order not found')));
			return;
		}

		$order_id = (int)$this->session->data['order_id'];

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);

		if (!$order_info) {
			$this->response->setOutput(json_encode(array('error' => 'Order not found')));
			return;
		}

		$order_products = $this->getOrderProducts($order_id);

		$paydo_order_items = array();
		foreach ($order_products as $product) {
			$paydo_order_items[] = array(
				'id'    => (string)$product['order_product_id'],
				'name'  => trim($product['name'] . ' ' . $product['model']),
				'price' => (float)$product['price'],
			);
		}

		$amount = number_format((float)$order_info['total'], 2, '.', '');
		$paydo_lang = $this->getPaydoLanguageCode();

		$request = array(
			'publicKey' => (string)$this->config->get('paydo_public_id'),
			'order'     => array(
				'id'          => (string)$order_info['order_id'],
				'amount'      => $amount,
				'currency'    => (string)$order_info['currency_code'],
				'description' => 'Payment order #' . (string)$order_info['order_id'],
				'items'       => $paydo_order_items,
			),
			'payer'     => array(
				'email' => (string)$order_info['email'],
				'phone' => (string)$order_info['telephone'],
				'name'  => trim((string)$order_info['firstname'] . ' ' . (string)$order_info['lastname']),
			),
			'resultUrl' => $this->url->link('checkout/success', '', true),
			'failPath'  => $this->url->link('checkout/failure', '', true),
			'language'  => $paydo_lang,
		);

		$request['signature'] = $this->generate_order_signature($request['order']);
		$this->ensureInvoiceTableExists();

		$wait_status_id = (int)$this->config->get('paydo_order_status_wait');
		if ($wait_status_id) {
			$this->model_checkout_order->addOrderHistory((int)$order_info['order_id'], $wait_status_id);
		}

		$invoice_id = $this->makeRequest($request);

		if ($invoice_id === '') {
			$this->response->setOutput(json_encode(array('error' => 'Invoice not created')));
			return;
		}

		$this->saveInvoiceRecord($order_info, $invoice_id);

		$redirect_url = "https://checkout.paydo.com/{$paydo_lang}/payment/invoice-preprocessing/" . rawurlencode($invoice_id);
		$this->response->setOutput(json_encode(array('redirect' => $redirect_url)));
	}

	public function callback() {
		$this->ensureInvoiceTableExists();

		if (!isset($this->request->server['REQUEST_METHOD']) || $this->request->server['REQUEST_METHOD'] !== 'POST') {
			return;
		}

		$raw = file_get_contents('php://input');
		$callback = json_decode($raw, true);

		if (!$callback || !is_array($callback)) {
			return;
		}

		$this->load->model('checkout/order');

		if (isset($callback['invoice'])) {
			if ($this->callback_check($callback) !== 'valid') {
				return;
			}

			$invoiceId = (string)$callback['invoice']['id'];
			$txid = $this->extractCallbackTxid($callback);
			$state = isset($callback['transaction']['state']) ? (int)$callback['transaction']['state'] : null;
			$callbackOrderId = isset($callback['transaction']['order']['id']) ? (int)$callback['transaction']['order']['id'] : 0;
			$stored_invoice = $this->getStoredInvoiceByInvoiceId($invoiceId);
			$orderId = !empty($stored_invoice['order_id']) ? (int)$stored_invoice['order_id'] : $callbackOrderId;
			$order_info = $orderId ? $this->model_checkout_order->getOrder($orderId) : array();

			if (!$order_info || !$this->isPaydoOrder($order_info)) {
				return;
			}

			if (!$this->isStoredInvoiceMatch($stored_invoice, $orderId, $invoiceId)) {
				return;
			}

			$invoice = $this->getInvoice($invoiceId);
			if (!$this->isValidInvoiceCallback($invoice, $invoiceId, $order_info, $state)) {
				return;
			}

			$this->updateInvoiceRecord($orderId, $invoiceId, $txid, $state, $invoice);

			if ($orderId && $state !== null) {
				if ($state === 2) {
					$success_status_id = (int)$this->config->get('paydo_order_status_success');
					if ($success_status_id) {
						$this->model_checkout_order->addOrderHistory($orderId, $success_status_id);
					}
				} elseif (in_array($state, array(3, 5), true)) {
					$error_status_id = (int)$this->config->get('paydo_order_status_error');
					if ($error_status_id) {
						$this->model_checkout_order->addOrderHistory($orderId, $error_status_id);
					}
				}
			}

			return;
		}

		if (isset($callback['orderId'], $callback['amount'], $callback['currency'], $callback['status'], $callback['signature'])) {
			$signature = $this->generate_legacy_signature(
				$callback['orderId'],
				$callback['amount'],
				$callback['currency'],
				(string)$this->config->get('paydo_secret_key'),
				$callback['status']
			);

			if (hash_equals((string)$callback['signature'], (string)$signature)) {
				$order_id = (int)$callback['orderId'];
				$order_info = $order_id ? $this->model_checkout_order->getOrder($order_id) : array();
				$stored_invoice = $order_id ? $this->getStoredInvoiceByOrderId($order_id) : array();

				if (!$order_info || !$this->isPaydoOrder($order_info)) {
					return;
				}

				if (empty($stored_invoice)) {
					return;
				}

				if (
					!$this->isSameOrderAmount($callback['amount'], $order_info['total']) ||
					strtoupper((string)$callback['currency']) !== strtoupper((string)$order_info['currency_code'])
				) {
					return;
				}

				if ($callback['status'] === 'success') {
					$success_status_id = (int)$this->config->get('paydo_order_status_success');
					if ($success_status_id) {
						$this->model_checkout_order->addOrderHistory($order_id, $success_status_id);
					}
				} elseif ($callback['status'] === 'error') {
					$error_status_id = (int)$this->config->get('paydo_order_status_error');
					if ($error_status_id) {
						$this->model_checkout_order->addOrderHistory($order_id, $error_status_id);
					}
				}
			}
		}
	}

	private function getOrderProducts($order_id) {
		$query = $this->db->query(
			"SELECT order_product_id, name, model, price
			 FROM `" . DB_PREFIX . "order_product`
			 WHERE order_id = " . (int)$order_id . "
			 ORDER BY order_product_id ASC"
		);

		return isset($query->rows) && is_array($query->rows) ? $query->rows : array();
	}

	private function getPaydoLanguageCode() {
		$lang_code = strtolower((string)$this->config->get('config_language'));
		return (strpos($lang_code, 'ru') === 0) ? 'ru' : 'en';
	}

	private function callback_check($callback) {
		$invoiceId = isset($callback['invoice']['id']) ? $callback['invoice']['id'] : null;
		$state = isset($callback['transaction']['state']) ? $callback['transaction']['state'] : null;

		if (!$invoiceId) return 'Empty invoice id';
		if (!is_numeric($state) || (int)$state < 1 || (int)$state > 5) return 'State is not valid';

		return 'valid';
	}

	private function extractCallbackTxid($callback) {
		if (isset($callback['invoice']['txid']) && $callback['invoice']['txid'] !== '') {
			return (string)$callback['invoice']['txid'];
		}

		if (isset($callback['transaction']['txid']) && $callback['transaction']['txid'] !== '') {
			return (string)$callback['transaction']['txid'];
		}

		if (isset($callback['transaction']['transactionIdentifier']) && $callback['transaction']['transactionIdentifier'] !== '') {
			return (string)$callback['transaction']['transactionIdentifier'];
		}

		return '';
	}

	private function isPaydoOrder($order_info) {
		$payment_code = isset($order_info['payment_code']) ? strtolower((string)$order_info['payment_code']) : '';
		$payment_method = isset($order_info['payment_method']) ? strtolower((string)$order_info['payment_method']) : '';

		if ($payment_code === 'paydo') {
			return true;
		}

		return strpos($payment_method, 'paydo') !== false;
	}

	private function isStoredInvoiceMatch($stored_invoice, $orderId, $invoiceId) {
		if (empty($stored_invoice)) {
			return false;
		}

		return (
			isset($stored_invoice['order_id'], $stored_invoice['invoice_id']) &&
			(int)$stored_invoice['order_id'] === (int)$orderId &&
			hash_equals((string)$stored_invoice['invoice_id'], (string)$invoiceId)
		);
	}

	private function isValidInvoiceCallback($invoice, $invoiceId, $order_info, $state) {
		if (!is_array($invoice)) {
			return false;
		}

		$invoice_identifier = isset($invoice['identifier']) ? (string)$invoice['identifier'] : '';
		$invoice_order_id = isset($invoice['orderIdentifier']) ? (string)$invoice['orderIdentifier'] : '';
		$invoice_currency = isset($invoice['currency']) ? strtoupper((string)$invoice['currency']) : '';
		$invoice_status = isset($invoice['status']) ? (int)$invoice['status'] : null;

		if ($invoice_identifier === '' || !hash_equals($invoice_identifier, (string)$invoiceId)) {
			return false;
		}

		if ($invoice_order_id === '' || !hash_equals($invoice_order_id, (string)$order_info['order_id'])) {
			return false;
		}

		if ($invoice_currency !== strtoupper((string)$order_info['currency_code'])) {
			return false;
		}

		if (!$this->isSameOrderAmount(isset($invoice['amount']) ? $invoice['amount'] : null, $order_info['total'])) {
			return false;
		}

		if ((int)$state === 2 && $invoice_status !== 1) {
			return false;
		}

		return true;
	}

	private function isSameOrderAmount($left, $right) {
		return number_format((float)$left, 2, '.', '') === number_format((float)$right, 2, '.', '');
	}

	private function getInvoice($invoiceId) {
		if (!$this->isValidInvoiceIdentifier($invoiceId)) {
			return array();
		}

		$response = $this->sendApiRequest(
			'https://api.paydo.com/v1/invoices/' . rawurlencode((string)$invoiceId),
			'GET'
		);

		if (!is_array($response) || !isset($response['data']) || !is_array($response['data'])) {
			return array();
		}

		return $response['data'];
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

	private function getStoredInvoiceByOrderId($orderId) {
		$query = $this->db->query(
			"SELECT *
			 FROM `" . DB_PREFIX . "paydo_invoice`
			 WHERE order_id = " . (int)$orderId . "
			 LIMIT 1"
		);

		return isset($query->row) && is_array($query->row) ? $query->row : array();
	}

	private function getStoredInvoiceByInvoiceId($invoiceId) {
		$query = $this->db->query(
			"SELECT *
			 FROM `" . DB_PREFIX . "paydo_invoice`
			 WHERE invoice_id = '" . $this->db->escape((string)$invoiceId) . "'
			 LIMIT 1"
		);

		return isset($query->row) && is_array($query->row) ? $query->row : array();
	}

	private function saveInvoiceRecord($order_info, $invoiceId) {
		$this->db->query(
			"INSERT INTO `" . DB_PREFIX . "paydo_invoice`
			 SET order_id = " . (int)$order_info['order_id'] . ",
			     invoice_id = '" . $this->db->escape((string)$invoiceId) . "',
			     amount = '" . $this->db->escape(number_format((float)$order_info['total'], 4, '.', '')) . "',
			     currency = '" . $this->db->escape((string)$order_info['currency_code']) . "',
			     date_created = NOW(),
			     date_modified = NOW()
			 ON DUPLICATE KEY UPDATE
			     invoice_id = VALUES(invoice_id),
			     amount = VALUES(amount),
			     currency = VALUES(currency),
			     date_modified = VALUES(date_modified)"
		);
	}

	private function updateInvoiceRecord($orderId, $invoiceId, $txid, $state, $invoice) {
		$invoice_status = isset($invoice['status']) ? (int)$invoice['status'] : 'NULL';
		$amount = isset($invoice['amount']) ? number_format((float)$invoice['amount'], 4, '.', '') : '0.0000';
		$currency = isset($invoice['currency']) ? (string)$invoice['currency'] : '';

		$this->db->query(
			"UPDATE `" . DB_PREFIX . "paydo_invoice`
			 SET txid = '" . $this->db->escape((string)$txid) . "',
			     amount = '" . $this->db->escape($amount) . "',
			     currency = '" . $this->db->escape($currency) . "',
			     invoice_status = " . ($invoice_status === 'NULL' ? 'NULL' : (int)$invoice_status) . ",
			     transaction_state = " . (int)$state . ",
			     date_modified = NOW()
			 WHERE order_id = " . (int)$orderId . "
			   AND invoice_id = '" . $this->db->escape((string)$invoiceId) . "'"
		);
	}

	private function makeRequest($request = array()) {
		$payload = json_encode($request, JSON_UNESCAPED_UNICODE);

		$json = $this->sendApiRequest('https://api.paydo.com/v1/invoices/create', 'POST', $payload);
		if (!is_array($json)) {
			return '';
		}

		if (isset($json['data']) && is_string($json['data']) && $json['data'] !== '') {
			return (string)$json['data'];
		}

		$id = isset($json['data']['invoice']['identifier']) ? $json['data']['invoice']['identifier']
			: (isset($json['invoice']['identifier']) ? $json['invoice']['identifier']
			: (isset($json['identifier']) ? $json['identifier'] : ''));

		$id = $id ? (string)$id : '';

		return $this->isValidInvoiceIdentifier($id) ? $id : '';
	}

	private function sendApiRequest($url, $method, $payload = '') {
		if (!$this->curl) {
			$this->curl = curl_init();
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->curl, CURLOPT_HEADER, false);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);
		}

		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);

		if ($method === 'POST') {
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $payload);
		} else {
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, null);
		}

		$response = curl_exec($this->curl);

		if ($response === false) {
			curl_close($this->curl);
			$this->curl = null;
			return array();
		}

		$code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		curl_close($this->curl);
		$this->curl = null;

		if ($code < 200 || $code >= 300) {
			return array();
		}

		$json = json_decode($response, true);
		return is_array($json) ? $json : array();
	}

	private function isValidInvoiceIdentifier($invoiceId) {
		return (bool)preg_match(
			'/^[a-f0-9]{8}-[a-f0-9]{4}-[1-5][a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/i',
			(string)$invoiceId
		);
	}

	private function generate_order_signature($order) {
		$sign_str = array(
			'amount'   => (string)$order['amount'],
			'currency' => (string)$order['currency'],
			'id'       => (string)$order['id'],
		);

		ksort($sign_str, SORT_STRING);
		$sign_data = array_values($sign_str);
		$sign_data[] = (string)$this->config->get('paydo_secret_key');

		return hash('sha256', implode(':', $sign_data));
	}

	private function generate_legacy_signature($orderId, $amount, $currency, $secretKey, $status) {
		$sign_str = array(
			'id'       => (string)$orderId,
			'amount'   => (string)$amount,
			'currency' => (string)$currency,
		);

		ksort($sign_str, SORT_STRING);
		$sign_data = array_values($sign_str);

		if ($status) {
			$sign_data[] = (string)$status;
		}

		$sign_data[] = (string)$secretKey;

		return hash('sha256', implode(':', $sign_data));
	}
}
