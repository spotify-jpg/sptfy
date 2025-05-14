<?php
// Configurações da API
$api_url = 'https://pay.pagparadise.com/api/v1/transaction.purchase';
$secret_key = '9ab17365-5ff0-4d64-bc2a-7888fce8a8f8'; // sua chave secreta

// Recebe o JSON enviado pelo front-end
$data = json_decode(file_get_contents("php://input"), true);

// Monta o corpo da requisição
$payload = [
    "name" => $data['name'],
    "email" => $data['email'],
    "cpf" => $data['cpf'],
    "phone" => $data['phone'],
    "paymentMethod" => "PIX",
    "amount" => $data['amount'],
    "traceable" => true,
    "items" => [
        [
            "unitPrice" => $data['amount'],
            "title" => $data['item_title'] ?? "Pagamento",
            "quantity" => 1,
            "tangible" => false
        ]
    ],
    "utmQuery" => $data['utmQuery'] ?? null,
    "referrerUrl" => $data['referrerUrl'] ?? null
];

// Prepara a requisição
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: ' . $secret_key
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Envia
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Trata resposta
if ($http_code === 200) {
    $res = json_decode($response, true);

    // Expiração manual: 30 minutos a partir de agora
    $manualExpiration = date("c", strtotime("+15 minutes"));

    echo json_encode([
        "success" => true,
        "id" => $res['id'],
        "transaction_id" => $res['transactionId'],
        "expiration" => $manualExpiration,
        "amount" => $res['amount'],
        "pix_data" => [
            "qrCode" => $res['pixQrCode'],
            "qrCodeText" => $res['pixCode']
        ]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Erro ao gerar PIX. Código HTTP: $http_code"
    ]);
}
?>