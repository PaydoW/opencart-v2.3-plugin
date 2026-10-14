Opencart v2.3 Paydo Payment Gateway
=====================

## Brief Description

Adds the ability to accept payments in OpenCart via PayDo.

## Requirements

-  Opencart 2.3+


## Installation Guide for Paydo in OpenCart 2 (via Admin Panel)

### 1. Download the Latest Version
- Go to the [Releases page](https://github.com/PaydoW/opencart-v2.3-plugin/releases)
- Select the latest version from the list of releases (the one at the top is the most recent).
- Download the `paydo_payment.ocmod.zip` file.

### 2. Install the Module via OpenCart Admin Panel
1. Log in to your **OpenCart admin panel**.
2. Navigate to **Extensions → Installer**.
3. Click the **Upload** button and select the downloaded `paydo_payment.ocmod.zip` file.
4. Wait for the installation to complete.

### 3. Enable and Configure the Module
1. Go to **Extensions → Extensions**.
2. In the dropdown menu, select **Payments**.
3. Find **Paydo Payment Gateway** and click the **Install** button.
4. After installation, click the **Edit** button.
5. Enter your **Public Key** and **Secret Key** (see the section below for details on how to obtain them).
6. In the module settings, the **IPN URL** field will automatically generate a link for processing payments. Copy this link.
7. Go to **Paydo.com → IPN → Add new IPN**, paste the copied link, and save it.

### Optional: let customers choose a PayDo payment method

1. Enable **Payment method selection** in the PayDo extension settings.
2. Keep **PayDo Hosted Page** enabled if customers should also be able to choose from all eligible methods on the PayDo-hosted page.
3. To offer specific methods directly, enter the PayDo **Project ID** and **JWT token**.
4. Click **Sync from PayDo** to load the enabled payment methods for the project.
5. Select the specific methods that should be available to customers.
6. Save the extension settings.

For the Hosted Page option, the plugin omits `paymentMethod`, so PayDo displays every method eligible for the payer. For a specific option, the selected numeric identifier is validated against the saved list and sent to PayDo as `paymentMethod`.

The plugin uses the current PayDo `/v3/invoices` and `/v3/instrument-settings/payment-methods` endpoints.

### 4. Refresh Modifications
1. Navigate to **Extensions → Modifications**.
2. Click the **Refresh** button at the top right to apply changes.

---

## How to get Public/Secret Keys and configure IPN in Paydo

### Public/Secret Keys
1. Log in to your account on **Paydo.com**.
2. Go to **Overview → Project (website) details**.
3. Open the **General information** tab.
4. Copy your **Public Key** and **Secret Key** and paste them into the module settings in OpenCart.

### IPN (payment notifications)
1. In your Paydo dashboard go to **IPN settings**.
2. Click **Add new IPN**.
3. Paste the **IPN URL** from the module settings in OpenCart.
4. Save.

> **Note:** Without correct IPN setup your OpenCart store will not automatically receive payment status updates.

---

## Support

* [Open an issue](https://github.com/PaydoW/opencart-v2.3-plugin/issues) if you are having issues with this plugin.
* [PayDo Documentation](https://github.com/PaydoW/paydo-api-doc)
* [Contact PayDo support](https://paydo.com/contacts-page-customer-support/)
  
**TIP**: When contacting support it will help us if you provide:

* OpenCart Version
* Other plugins you have installed
  * Some plugins do not play nice
* Configuration settings for the plugin (Most merchants take screenshots)
* Any log files that will help
  * Web server error logs
* Screenshots of error message if applicable.

## Contribute

Would you like to help with this project?  Great!  You don't have to be a developer, either.
If you've found a bug or have an idea for an improvement, please open an
[issue](https://github.com/PaydoW/opencart-v2.3-plugin/issues) and tell us about it.

If you *are* a developer wanting contribute an enhancement, bugfix or other patch to this project,
please fork this repository and submit a pull request detailing your changes.  We review all PRs!

This open source project is released under the [MIT license](http://opensource.org/licenses/MIT)
which means if you would like to use this project's code in your own project you are free to do so.


## License

Please refer to the [LICENSE](https://github.com/PaydoW/opencart-v2.3-plugin/blob/master/LICENSE) file that came with this project.

## Version 2.2.0

- PayDo API v3 for invoices, payment methods and transaction verification.
- Optional Hosted Page and individual methods, including project-specific card acquiring.
- Both flat and nested payment-method API responses are supported.
- Card notifications can omit `transaction.state`. A verified paid invoice confirms settlement even when the checkout transaction endpoint still reports pending. Invoice ID, order ID, amount, currency and transaction ID are checked before updating the order.
- Repeated payment requests reuse the saved invoice. To change the method after invoice creation, place a new order.
- The return from PayDo also checks the checkout session's saved invoice through the API, without trusting redirect parameters. Configure the IPN URL for updates when customers do not return to the store.
- Duplicate notifications do not repeat order history; delayed failures cannot reverse a verified payment. Temporary API verification failures return HTTP 503 so PayDo can retry.
- Order amounts are converted using the order's saved OpenCart currency rate before signing.
