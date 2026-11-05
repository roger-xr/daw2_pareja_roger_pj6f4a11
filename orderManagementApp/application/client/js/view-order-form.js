function checkOrderCode(event) {
  const code = document.getElementById('id').value;

  if (code.trim() === '') {
    event.preventDefault();
    const box = document.getElementById('searchMessage');
    box.textContent = 'Please enter an order code.';
    box.className = 'message error';
  }
}

document.getElementById('searchForm').addEventListener('submit', checkOrderCode);
