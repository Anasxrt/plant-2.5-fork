<?php
/**
 * LINE Notify Integration
 * Send notifications to LINE when forms are submitted
 * 
 * Uses WordPress stubs from: https://github.com/php-stubs/wordpress-stubs
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Send LINE notification message.
 *
 * @param string $message Message to send (will be sanitized).
 * @param string $image_path Optional image file path.
 * @param string $token LINE Notify token.
 */
function line_notify_send_message(string $message, string $image_path, string $token): void {
    $api_url = 'https://notify-api.line.me/api/notify';
    
    $message_data = ['message' => sanitize_text_field($message)];
    
    if (!empty($image_path) && file_exists($image_path)) {
        $message_data['imageFile'] = curl_file_create($image_path);
    }
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api_url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $message_data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: multipart/form-data',
        'Authorization: Bearer ' . sanitize_text_field($token)
    ));
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    
    $result = curl_exec($curl);
    
    if (curl_errno($curl)) {
        error_log('LINE Notify Error: ' . curl_error($curl));
    }
    
    curl_close($curl);
}

/**
 * Extract field keys from settings.
 *
 * @param int $page_id ACF page ID.
 * @return array<int, string> Field keys.
 */
function line_get_field_keys(int $page_id): array {
    $fields = get_field('fields', $page_id);
    
    if (!$fields) {
        return ['textarea-1', 'radio-1', 'name-1', 'email-1', 'number-2', 'upload-1', 'textarea-2'];
    }
    
    return array_map('sanitize_text_field', explode(',', $fields));
}

/**
 * Extract field labels from settings.
 *
 * @param int $page_id ACF page ID.
 * @return array<int, string> Labels.
 */
function line_get_field_labels(int $page_id): array {
    $labels = get_field('labels', $page_id);
    
    if (!$labels) {
        return ['คำสั่งซื้อ', 'ช่องทางการชำระเงิน', 'ชื่อ', 'อีเมล', 'เบอร์โทร', 'สลิป', 'ที่อยู่'];
    }
    
    return array_map('sanitize_text_field', explode(',', $labels));
}

/**
 * Build message from form entry.
 *
 * @param object $entry Form entry object.
 * @param array<int, string> $fields Field keys to extract.
 * @param array<int, string> $labels Field labels.
 * @return array{message: string, image: string} Message and image path.
 */
function line_build_message(object $entry, array $fields, array $labels): array {
    $message = '#' . intval($entry->entry_id);
    $image_path = '';
    
    $meta = (array) $entry->meta_data;
    
    foreach ($meta as $key => $value) {
        if (!in_array($key, $fields, true)) {
            continue;
        }
        
        $label_index = array_search($key, $fields, true);
        if ($label_index === false) {
            continue;
        }
        $label = $labels[$label_index];
        
        if ($key !== 'upload-1') {
            $message .= "\n" . $label . ': ' . sanitize_text_field($value['value']);
        } else {
            $file_data = $value['value'];
            $file_path = isset($file_data['file']['file_path'][0]) ? $file_data['file']['file_path'][0] : '';
            
            if (preg_match('/\.(png|jpg|jpeg)$/i', $file_path)) {
                $image_path = $file_path;
            } else {
                $file_url = isset($file_data['file']['file_url'][0]) ? esc_url_raw($file_data['file']['file_url'][0]) : '';
                $message .= "\n" . $label . ': ' . $file_url;
            }
        }
    }
    
    return array('message' => $message, 'image' => $image_path);
}

/**
 * Find matching entry by email and phone.
 *
 * @param array<int, object> $entries Form entries.
 * @param string $email Email to match.
 * @param string $phone Phone to match.
 * @return object|null Matching entry or null.
 */
function line_find_entry(array $entries, string $email, string $phone): ?object {
    $count = 0;
    
    foreach ($entries as $entry) {
        $count++;
        $meta = (array) $entry->meta_data;
        
        if (isset($meta['email-1']['value']) && isset($meta['number-2']['value'])) {
            if (sanitize_text_field($meta['email-1']['value']) === sanitize_text_field($email) &&
                sanitize_text_field($meta['number-2']['value']) === sanitize_text_field($phone)) {
                return $entry;
            }
        }
        
        if ($count > 5) {
            break;
        }
    }
    
    return null;
}

/**
 * Handle form submission and send LINE notification.
 *
 * @param int $form_id Form identifier.
 * @param array<string, mixed> $response Form submission response.
 */
function line_handle_form_submit(int $form_id, array $response): void {
    if (empty($response['success'])) {
        return;
    }
    
    $vars = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    if (!is_array($vars)) {
        return;
    }
    
    $page_id = isset($vars['page_id']) ? intval($vars['page_id']) : 0;
    $token = get_field('line_notify_token', $page_id);
    
    if (!$token) {
        return;
    }
    
    $fields = line_get_field_keys($page_id);
    $labels = line_get_field_labels($page_id);
    
    $entries = Forminator_API::get_entries($form_id);
    
    $email = isset($vars['email-1']) ? sanitize_text_field($vars['email-1']) : '';
    $phone = isset($vars['number-2']) ? sanitize_text_field($vars['number-2']) : '';
    
    $entry = line_find_entry($entries, $email, $phone);
    
    if (!$entry) {
        return;
    }
    
    $data = line_build_message($entry, $fields, $labels);
    line_notify_send_message($data['message'], $data['image'], $token);
}

// Hook into Forminator form submissions.
add_action('forminator_form_after_handle_submit', 'line_handle_form_submit', 10, 2);
add_action('forminator_form_after_save_entry', 'line_handle_form_submit', 10, 2);