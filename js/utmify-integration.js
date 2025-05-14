
function enviarVendaUtmify({ status, transactionId, createdAt, approvedAt, customer, product, utm, commission }) {
  const payload = {
    platform: "pix",
    paymentMethod: "pix",
    status: status === "APPROVED" ? "paid" : "waiting_payment",
    orderId: transactionId,
    createdAt: createdAt,
    approvedDate: approvedAt || null,
    customer: {
      name: customer.name,
      email: customer.email,
      document: customer.cpf,
      phone: customer.phone
    },
    product: {
      id: product.id || "prod-001",
      name: product.name || "Pagamento de Serviço",
      planId: product.planId || "plano01",
      planName: product.planName || "Plano Mensal",
      quantity: product.quantity || 1,
      priceInCents: product.price || 1990
    },
    trackingParameters: {
      utm_source: utm.utm_source || null,
      utm_campaign: utm.utm_campaign || null,
      utm_content: utm.utm_content || null,
      utm_term: utm.utm_term || null,
      utm_medium: utm.utm_medium || null
    },
    commission: {
      affiliateId: commission.affiliateId || null,
      totalPriceInCents: commission.totalPriceInCents || product.price || 1990,
      gatewayFeeInCents: commission.gatewayFeeInCents || 0,
      userCommissionInCents: commission.userCommissionInCents || 0
    }
  };

  fetch("https://api.utmify.com.br/api-credentials/orders", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "x-api-token": "RLal4ynifQxqS3HqpVFxakzxGlGRBGznqNf1"
    },
    body: JSON.stringify(payload)
  })
  .then(response => {
    if (!response.ok) {
      return response.text().then(text => {
        console.error("Erro na UTMify:", text);
      });
    }
    return response.json();
  })
  .then(data => {
    console.log("Evento enviado para UTMify:", data);
  })
  .catch(err => {
    console.error("Falha na integração com a UTMify:", err);
  });
}
