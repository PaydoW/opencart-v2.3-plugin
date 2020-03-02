<?php

/**
 * Class ControllerExtensionPaymentPaydo
 */
class ControllerExtensionPaymentPaydo extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        $this->load->model('checkout/order');
        $this->load->language('extension/payment/paydo');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $orderData = [];
        $orderData['publicKey'] = $this->config->get('paydo_public');
        $orderData['order'] = [];
        $orderData['order']['id'] = strval($this->session->data['order_id']);
        $orderData['order']['amount'] = number_format($this->currency->format($order_info['total'], $order_info['currency_code'], '', false), 4, ".", "");
        $orderData['order']['currency'] = $order_info['currency_code'];
        $orderData['order']['description'] = 'Payment order #' . $this->session->data['order_id'];
        $orderData['order']['items'] = [];
        $orderData['payer'] = [];
        $orderData['payer']['email'] = $order_info['email'];
        $orderData['payer']['name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        $orderData['payer']['phone'] = $order_info['telephone'];
        $orderData['language'] = ($this->session->data['language'] == 'ru_ru') ? 'ru' : 'en';
        $orderData['resultUrl'] = $this->url->link('checkout/success');
        $orderData['failPath'] = $this->url->link('checkout/failure');

        /** generate signature */
        $orderData['signature'] = $this->generate_signature(
            $this->session->data['order_id'],
            number_format($this->currency->format($order_info['total'],
            $order_info['currency_code'], '', false), 4), $order_info['currency_code'],
            $this->config->get('paydo_secret'),
            false
        );
        $invoice_id = $this->apiRequest($orderData);

        $data['action'] = "https://paydo.com/{$orderData['language']}/payment/invoice-preprocessing/{$invoice_id}";
        return $this->load->view('extension/payment/paydo', $data);
    }

    /**
     * @param $orderData array
     * @return mixed
     */
    private function apiRequest($orderData)
    {
        $apiUrl = 'https://paydo.com/v1/invoices/create';
        $data = json_encode($orderData);
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($result, 0, $header_size);
        $headers = explode("\r\n", $headers);
        $invoice_identifier = preg_grep("/^identifier/", $headers);
        $invoice_identifier = implode(',' , $invoice_identifier);
        $invoice_identifier = substr($invoice_identifier, strrpos($invoice_identifier, ':')+2);
        curl_close($ch);
        return $invoice_identifier;
    }

    /**
     * get transaction info and update order status
     */
    public function callback()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $callback = json_decode(file_get_contents('php://input'));
            $callback = json_encode($callback);
            $callback = json_decode($callback, false);
            if (is_object($callback)) {
                if(isset($callback->invoice)) {
                    $this->load->model('checkout/order');
                    if ($this->callback_check($callback) === 'valid'){
                        if($callback->transaction->state === 2) {
                            $this->model_checkout_order->addOrderHistory($callback->transaction->order->id, $this->config->get('paydo_order_status_id'));
                        } elseif ($callback->transaction->state === 3 or $callback->transaction->state === 5) {
                            $this->model_checkout_order->addOrderHistory($callback->transaction->order->id, $this->config->get('paydo_failed_status_id'));
                        }
                    } else {
                        $this->model_checkout_order->addOrderHistory($callback->orderId, $this->config->get('paydo_pending_status_id'));
                        $this->log->write('Error callback: '. $this->callback_check($callback));
                    }
                } else {
                    $this->load->model('checkout/order');
                    $signature = $this->generate_signature($callback->orderId, $callback->amount, $callback->currency, $this->config->get('paydo_secret'), $callback->status);
                    if ($callback->signature == $signature) {
                        if ($callback->status === 'success') {
                            $this->model_checkout_order->addOrderHistory($callback->orderId, $this->config->get('paydo_order_status_id'));
                        }
                        else if ($callback->status === 'error') {
                            $this->model_checkout_order->addOrderHistory($callback->orderId, $this->config->get('paydo_failed_status_id'));
                        }
                    } else {
                        $this->log->write('Error callback!');
                        $this->model_checkout_order->addOrderHistory($callback->orderId, $this->config->get('paydo_pending_status_id'));
                    }
                }
            } else {
                $this->log->write('Error. Callback is not an object');
            }
        } else {
            $this->log->write('Invalid server request');
        }
    }

    /**
     * @return boolean
     * @param $callback object
     */
    private function callback_check($callback)
    {
        $invoiceId = !empty($callback->invoice->id) ? $callback->invoice->id : null;
        $txid = !empty($callback->invoice->txid) ? $callback->invoice->txid : null;
        $orderId = !empty($callback->transaction->order->id) ? $callback->transaction->order->id : null;
        $state = !empty($callback->transaction->state) ? $callback->transaction->state : null;


        if (!$invoiceId) {
            return 'Empty invoice id';
        }
        if (!$txid) {
            return 'Empty transaction id';
        }
        if (!$orderId) {
            return 'Empty order id';
        }
        if (!(1 <= $state && $state <= 5)) {
            return 'State is not valid';
        }
        return 'valid';
    }

    /**
     * @return string
     */
    private function generate_signature($orderId, $amount, $currency, $secretKey, $status)
    {
        $sign_str = ['id' => $orderId, 'amount' => $amount, 'currency' => $currency];
        ksort($sign_str, SORT_STRING);
        $sign_data = array_values($sign_str);
        if ($status){
            array_push($sign_data, $status);
        }
        array_push($sign_data, $secretKey);
        return hash('sha256', implode(':', $sign_data));
    }
}