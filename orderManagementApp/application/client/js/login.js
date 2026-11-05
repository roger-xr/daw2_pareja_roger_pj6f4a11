function showLoginMessage(text) {
  const box = document.getElementById('loginMessage');
  box.textContent = text;
  box.className = 'message error';
}

function fieldsAreFilled(user, password) {
  return user.trim() !== '' && password.trim() !== '';
}

function goToStartPage() {
  window.location.href = 'index.html';
}

async function sendLogin(event) {
  event.preventDefault();

  const user = document.getElementById('user').value;
  const password = document.getElementById('password').value;

  if (!fieldsAreFilled(user, password)) {
    showLoginMessage('Please enter your user name and your password.');
    return;
  }

  try {
    const response = await fetch('../server/login.php', {
      method: 'POST',
      body: new FormData(document.getElementById('loginForm'))
    });

    if (response.ok) {
      window.location.href = 'menu.html';
    } else {
      alert('Authentication error: the user could not be validated.');
      goToStartPage();
    }
  } catch (error) {
    alert('The server is not available. Please try again later.');
    goToStartPage();
  }
}

document.getElementById('loginForm').addEventListener('submit', sendLogin);
