<?php
// Configurações da API
$api_url_base = 'https://pay.pagparadise.com/api/v1/transaction.getPayment';
$secret_key = '9ab17365-5ff0-4d64-bc2a-7888fce8a8f8'; // sua chave secreta

// Lê o JSON do frontend
$data = json_decode(file_get_contents("php://input"), true);

// Verifica se o ID foi passado
if (!isset($data['id'])) {
    echo json_encode([
        "success" => false,
        "error" => "ID da transação não fornecido."
    ]);
    exit;
}

// Constrói a URL com o ID
$api_url = $api_url_base . '?id=' . urlencode($data['id']);

// Prepara a requisição cURL
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $secret_key
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Trata a resposta
if ($http_code === 200) {
    $res = json_decode($response, true);
    echo json_encode([
        "success" => true,
        "status" => $res['status'] ?? 'UNKNOWN',
        "method" => $res['method'] ?? null,
        "amount" => $res['amount'] ?? null,
        "customer" => $res['customer'] ?? null
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Erro ao verificar pagamento. Código HTTP: $http_code"
    ]);
}
?>