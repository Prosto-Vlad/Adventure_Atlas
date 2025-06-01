document.addEventListener('DOMContentLoaded', function () {

    const authButton = document.getElementById('authButton');
    const logoutButton = document.getElementById('logoutButton');

    /*
        Функції для взаємодії з користувачем
    */

    if (authButton != null) {
        authButton.addEventListener('click', function (event) {
            //const isRegister = $('#emailField').is(':visible'); //виглядає не дуже, треба переробити
            // const url = isRegister ? '/auth/register' : '/auth/login';
            const data = {
                username: $('#username').val(),
                password: $('#password').val(),
                // email: isRegister ? $('#emailField').val() : undefined
            };

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/auth/login', true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Помилка!');
                    }
                } else {
                    console.error('Network request failed');
                    alert('Не вдалося виконати запит.');
                }
            };

            xhr.onerror = function () {
                console.error('Network error occurred');
                alert('Сталася помилка мережі.');
            };

            xhr.send(JSON.stringify(data));
        });
    }

    if (logoutButton != null) {
        logoutButton.addEventListener('click', function (event) {
            event.preventDefault();
            logout();
        });
    }

    function logout() {
        $.ajax({
            url: '/auth/logout',
            method: 'POST',
            success: function (response) {
                if (response.success) {
                    window.location.href = '/';
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
            }
        });
    }
});