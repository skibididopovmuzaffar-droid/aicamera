<?php
// Разрешаем запросы (чтобы не было ошибки CORS при локальном тестировании)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// ВСТАВЬТЕ ВАШИ ДАННЫЕ СЮДА
$botToken = '9005989171:AAF7iOziW3xpFgb0pangWl6OmlqkDFsXhKM'; 
$chatId = '5450527103';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'] ?? 'Обнаружено нарушение';
    
    if (isset($_FILES['photo'])) {
        $photoPath = $_FILES['photo']['tmp_name'];
        $photoName = $_FILES['photo']['name'];

        // Формируем данные для отправки в Telegram API
        $postFields = [
            'chat_id' => $chatId,
            'caption' => $message,
            'parse_mode' => 'HTML',
            'photo' => new CURLFile($photoPath, 'image/jpeg', $photoName)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$botToken}/sendPhoto");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo json_encode(['status' => 'error', 'message' => curl_error($ch)]);
        } else {
            echo json_encode(['status' => 'success', 'telegram' => json_decode($response)]);
        }
        curl_close($ch);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Фото не получено']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Метод не поддерживается']);
}
?>
