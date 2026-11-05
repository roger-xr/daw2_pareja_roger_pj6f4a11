function goToSelectedOperation() {
  const select = document.getElementById('operation');
  window.location.href = select.value;
}

document.getElementById('goButton').addEventListener('click', goToSelectedOperation);
