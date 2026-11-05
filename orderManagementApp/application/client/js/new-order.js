function showOrderMessage(text, isError) {
  const box = document.getElementById('orderMessage');
  box.textContent = text;
  box.className = isError ? 'message error' : 'message success';
}

function toggleQuantity(checkbox) {
  const quantityInput = document.getElementById(checkbox.dataset.target);
  quantityInput.disabled = !checkbox.checked;
}

function hasSelectedProduct() {
  return document.querySelectorAll('.product-check:checked').length > 0;
}

function setUpProductChecks() {
  const checkboxes = document.querySelectorAll('.product-check');
  checkboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
      toggleQuantity(checkbox);
    });
  });
}

async function sendOrder(event) {
  event.preventDefault();

  if (!hasSelectedProduct()) {
    showOrderMessage('Please select at least one product.', true);
    return;
  }

  try {
    const response = await fetch('../server/create-order.php', {
      method: 'POST',
      body: new FormData(document.getElementById('orderForm'))
    });

    if (response.status === 404) {
      document.documentElement.innerHTML = await response.text();
      return;
    }

    if (!response.ok) {
      showOrderMessage('The order could not be saved.', true);
      return;
    }

    const totalWithVat = await response.text();
    showOrderMessage('Order saved. Total price with 21% VAT: ' + totalWithVat + ' EUR', false);
  } catch (error) {
    showOrderMessage('The server is not available. Please try again later.', true);
  }
}

setUpProductChecks();
document.getElementById('orderForm').addEventListener('submit', sendOrder);
