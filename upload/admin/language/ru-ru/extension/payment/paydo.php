<?php
// Heading
$_['heading_title'] = 'Paydo';
$_['heading_invoices'] = 'Инвойсы Paydo';

// Text
$_['text_payment']   = 'Платежи';
$_['text_extension'] = 'Расширения';
$_['text_success']   = 'Настройки Paydo успешно изменены!';
$_['text_edit']      = 'Редактирование Paydo';
$_['text_paydo']     = '<a href="https://paydo.com/ru/solution/" target="_blank"><img src="view/image/payment/paydo.png" alt="Paydo" title="Paydo" style="max-width:150px;" /></a>';
$_['text_no_results'] = 'Записи по инвойсам отсутствуют.';
$_['text_no_methods'] = 'Методы ещё не загружены. Укажите Project ID и JWT-токен, затем выполните синхронизацию.';
$_['text_syncing'] = 'Синхронизация...';
$_['text_sync_success'] = 'Синхронизировано методов: %s. Выберите нужные методы и сохраните настройки.';
$_['text_sync_error'] = 'Не удалось синхронизировать методы оплаты.';
$_['text_search_method'] = 'Поиск метода оплаты...';

// Entry
$_['entry_public']          = 'Публичный ключ';
$_['entry_secret']          = 'Секретный ключ';
$_['entry_complete_status'] = 'Статус заказа при успешной оплате';
$_['entry_pending_status']  = 'Статус заказа при ожидании оплаты';
$_['entry_failed_status']   = 'Статус заказа при неудачной оплате';
$_['entry_status']          = 'Статус';
$_['entry_sort_order']      = 'Порядок сортировки';
$_['entry_methods_mode']     = 'Выбор метода оплаты';
$_['entry_hosted_page']      = 'Общая Hosted Page PayDo';
$_['entry_project_id']       = 'Project ID';
$_['entry_jwt_token']        = 'JWT-токен';
$_['entry_enabled_methods']  = 'Разрешённые методы PayDo';

// Help
$_['help_methods_mode'] = 'Позволяет покупателю выбрать конкретный метод PayDo при оформлении заказа.';
$_['help_hosted_page'] = 'Показывать общую Hosted Page PayDo со всеми доступными покупателю методами. Для этой опции paymentMethod не передаётся.';
$_['help_project_id'] = 'Идентификатор проекта PayDo для синхронизации доступных методов оплаты.';
$_['help_jwt_token'] = 'Bearer-токен для синхронизации методов оплаты. Создание инвойса авторизуется подписью.';
$_['help_enabled_methods'] = 'На checkout отображаются только отмеченные методы. Новые методы после синхронизации не включаются автоматически.';

// Column
$_['column_order_id'] = 'ID заказа';
$_['column_invoice_id'] = 'ID инвойса';
$_['column_amount'] = 'Сумма';
$_['column_currency'] = 'Валюта';
$_['column_invoice_status'] = 'Статус инвойса';
$_['column_transaction_state'] = 'Статус транзакции';
$_['column_date_created'] = 'Создан';
$_['column_date_modified'] = 'Обновлён';

// Button
$_['button_invoices'] = 'Инвойсы';
$_['button_sync_methods'] = 'Синхронизировать с PayDo';

// Error
$_['error_permission'] = 'У вас нет прав для управления модулем Paydo!';
$_['error_public_id']  = 'Публичный ключ обязателен для заполнения!';
$_['error_secret_key'] = 'Секретный ключ обязателен для заполнения!';
$_['error_project_id'] = 'Для синхронизации методов необходимо указать Project ID!';
$_['error_jwt_token'] = 'Для синхронизации методов необходимо указать JWT-токен!';
$_['error_enabled_methods'] = 'Включите общую Hosted Page PayDo или выберите хотя бы один отдельный метод!';
$_['error_sync_credentials'] = 'Сначала укажите Project ID и JWT-токен.';
$_['error_sync_request'] = 'Не удалось отправить запрос в PayDo.';
$_['error_sync_api'] = 'API PayDo вернул HTTP-статус %s.';
$_['error_no_methods'] = 'В ответе PayDo не найдено включённых методов оплаты.';
