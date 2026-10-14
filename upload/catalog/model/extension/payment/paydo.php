<?php

/**
 * Class ModelExtensionPaymentPaydo
 */
class ModelExtensionPaymentPaydo extends Model
{
    /**
     * @param $address
     * @param $total
     * @return array
     */
    public function getMethod($address, $total)
    {
        $this->load->language('extension/payment/paydo');

        $status = (bool)$this->config->get('paydo_status');

        if ($status && $this->config->get('paydo_methods_mode')) {
            $available_methods = $this->config->get('paydo_available_methods');
            $enabled_methods = $this->config->get('paydo_enabled_methods');
            $hosted_page_enabled = $this->config->get('paydo_hosted_page_enabled');
            $has_enabled_method = $hosted_page_enabled === null ? true : (bool)$hosted_page_enabled;

            if (is_array($available_methods) && is_array($enabled_methods)) {
                foreach ($enabled_methods as $identifier) {
                    if (!is_scalar($identifier)) {
                        continue;
                    }
                    $identifier = (string)$identifier;
                    if (
                        preg_match('/^[1-9][0-9]*$/', $identifier) &&
                        isset($available_methods[$identifier]) &&
                        is_array($available_methods[$identifier]) &&
                        !empty($available_methods[$identifier]['title'])
                    ) {
                        $has_enabled_method = true;
                        break;
                    }
                }
            }

            if (!$has_enabled_method) {
                $status = false;
            }
        }

        $method_data = array();
        if ($status) {
            $method_data = array(
                'code' => 'paydo',
                'title' => $this->language->get('text_title'),
                'terms' => '',
                'sort_order' => $this->config->get('paydo_sort_order')
            );
        }
        return $method_data;
    }
}
