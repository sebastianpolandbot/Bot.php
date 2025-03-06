<?php // Configuración del bot $TOKEN = "8191447740:AAGuNa0H9VfuIGF71hDIPy2Tru99OqRmFNE"; // Reemplaza con tu token de Telegram $GEMINI_API_KEY = "AIzaSyCsbujK031D2GOWOADObq0MW9FRpfxPDiU"; // Reemplaza con tu clave de Gemini API

// Recibir datos de Telegram $update = json_decode(file_get_contents("php://input"), true);

if (isset($update["message"])) { $chat_id = $update["message"]["chat"]["id"]; $text = $update["message"]["text"];

// Respuesta predeterminada
$reply = "Hello! How can I assist you today? 😊";

// Si el usuario pregunta por predicciones de ventas
if (stripos($text, "sales prediction") !== false || stripos($text, "predicción de ventas") !== false) {
    $product_name = trim(str_ireplace(["sales prediction", "predicción de ventas"], "", $text));
    $reply = getSalesPrediction($product_name);
}

// Enviar mensaje de respuesta
sendMessage($chat_id, $reply);

}

// Función para enviar mensajes a Telegram function sendMessage($chat_id, $text) { global $TOKEN; $url = "https://api.telegram.org/bot$TOKEN/sendMessage?chat_id=$chat_id&text=" . urlencode($text); file_get_contents($url); }

// Función para obtener predicción de ventas con Gemini API function getSalesPrediction($product_name) { global $GEMINI_API_KEY; $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$GEMINI_API_KEY";

$data = json_encode([
    "contents" => [[
        "role" => "user",
        "parts" => [[
            "text" => "Please provide a detailed sales prediction for the product '$product_name' in 2025."
        ]]
    ]],
    "generationConfig" => [
        "maxOutputTokens" => 250,
        "temperature" => 0.7
    ]
]);

$options = [
    "http" => [
        "header" => "Content-Type: application/json\r\n",
        "method" => "POST",
        "content" => $data
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$response_json = json_decode($response, true);

if (isset($response_json['candidates'][0]['content']['parts'][0]['text'])) {
    return $response_json['candidates'][0]['content']['parts'][0]['text'];
} else {
    return "Sorry, I couldn't retrieve a sales prediction. Please try again later.";
}

} ?>

