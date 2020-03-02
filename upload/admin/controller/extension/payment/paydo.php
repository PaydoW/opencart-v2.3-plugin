<?php

/**
 * Class ControllerExtensionPaymentPaydo
 */
class ControllerExtensionPaymentPaydo extends Controller
{
    /**
     * @var array
     */
    private $error = array();

    /**
     *
     */
    public function index()
    {
        $this->load->language('extension/payment/paydo');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('paydo', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['entry_public'] = $this->language->get('entry_public');
        $data['entry_secret'] = $this->language->get('entry_secret');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_complete_status'] = $this->language->get('entry_complete_status');
        $data['entry_pending_status'] = $this->language->get('entry_pending_status');
        $data['entry_failed_status'] = $this->language->get('entry_failed_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['help_total'] = $this->language->get('help_total');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['public'])) {
            $data['error_public'] = $this->error['public'];
        } else {
            $data['error_public'] = '';
        }

        if (isset($this->error['secret'])) {
            $data['error_secret'] = $this->error['secret'];
        } else {
            $data['error_secret'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/paydo', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/paydo', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

        if (isset($this->request->post['paydo_public'])) {
            $data['paydo_public'] = $this->request->post['paydo_public'];
        } else {
            $data['paydo_public'] = $this->config->get('paydo_public');
        }

        if (isset($this->request->post['paydo_secret'])) {
            $data['paydo_secret'] = $this->request->post['paydo_secret'];
        } else {
            $data['paydo_secret'] = $this->config->get('paydo_secret');
        }


        if (isset($this->request->post['paydo_order_status_id'])) {
            $data['paydo_order_status_id'] = $this->request->post['paydo_order_status_id'];
        } else {
            $data['paydo_order_status_id'] = $this->config->get('paydo_order_status_id');
        }

        if (isset($this->request->post['paydo_pending_status_id'])) {
            $data['paydo_pending_status_id'] = $this->request->post['paydo_pending_status_id'];
        } else {
            $data['paydo_pending_status_id'] = $this->config->get('paydo_pending_status_id');
        }

        if (isset($this->request->post['paydo_failed_status_id'])) {
            $data['paydo_failed_status_id'] = $this->request->post['paydo_failed_status_id'];
        } else {
            $data['paydo_failed_status_id'] = $this->config->get('paydo_failed_status_id');
        }


        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['paydo_geo_zone_id'])) {
            $data['paydo_geo_zone_id'] = $this->request->post['paydo_geo_zone_id'];
        } else {
            $data['paydo_geo_zone_id'] = $this->config->get('paydo_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['paydo_status'])) {
            $data['paydo_status'] = $this->request->post['paydo_status'];
        } else {
            $data['paydo_status'] = $this->config->get('paydo_status');
        }

        if (isset($this->request->post['paydo_sort_order'])) {
            $data['paydo_sort_order'] = $this->request->post['paydo_sort_order'];
        } else {
            $data['paydo_sort_order'] = $this->config->get('paydo_sort_order');
        }


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/paydo', $data));
    }

    /**
     * @return bool
     */
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/paydo')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['paydo_public']) {
            $this->error['public'] = $this->language->get('error_public');
        }

        if (!$this->request->post['paydo_secret']) {
            $this->error['secret'] = $this->language->get('error_secret');
        }

        return !$this->error;
    }
}