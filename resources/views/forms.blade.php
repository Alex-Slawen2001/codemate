<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестирование API</title>
</head>
<body>
<h1>Тестирование API</h1>

<!-- Форма для пополнения баланса -->
<form id="depositForm">
    <h2>Пополнение баланса</h2>
    <label for="depositUserId">User ID:</label>
    <input type="number" id="depositUserId" name="user_id" required><br>
    <label for="depositAmount">Amount:</label>
    <input type="number" id="depositAmount" name="amount" required><br>
    <label for="depositComment">Comment (опционально):</label>
    <input type="text" id="depositComment" name="comment"><br>
    <button type="submit">Отправить</button>
</form>

<!-- Форма для снятия средств -->
<form id="withdrawForm">
    <h2>Снятие средств</h2>
    <label for="withdrawUserId">User ID:</label>
    <input type="number" id="withdrawUserId" name="user_id" required><br>
    <label for="withdrawAmount">Amount:</label>
    <input type="number" id="withdrawAmount" name="amount" required><br>
    <label for="withdrawComment">Comment (опционально):</label>
    <input type="text" id="withdrawComment" name="comment"><br>
    <button type="submit">Отправить</button>
</form>

<!-- Форма для перевода средств -->
<form id="transferForm">
    <h2>Перевод средств</h2>
    <label for="transferFromUserId">From User ID:</label>
    <input type="number" id="transferFromUserId" name="from_user_id" required><br>
    <label for="transferToUserId">To User ID:</label>
    <input type="number" id="transferToUserId" name="to_user_id" required><br>
    <label for="transferAmount">Amount:</label>
    <input type="number" id="transferAmount" name="amount" required><br>
    <label for="transferComment">Comment (опционально):</label>
    <input type="text" id="transferComment" name="comment"><br>
    <button type="submit">Отправить</button>
</form>

<!-- Форма для получения баланса -->
<form id="balanceForm">
    <h2>Получение баланса</h2>
    <label for="balanceUserId">User ID:</label>
    <input type="number" id="balanceUserId" name="user_id" required><br>
    <button type="submit">Отправить</button>
</form>

<script>
    // Функция для отправки формы
    async function submitForm(event, url) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        try {
            const response = await fetch(url, {
                method: event.target.id === 'balanceForm' ? 'GET' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: event.target.id === 'balanceForm' ? null : JSON.stringify(data),
            });

            const result = await response.json();
            console.log(result);
            alert(JSON.stringify(result, null, 2));
        } catch (error) {
            console.error('Ошибка:', error);
        }
    }

    document.getElementById('depositForm').addEventListener('submit', (e) => submitForm(e, 'http://localhost:8000/api/deposit'));
    document.getElementById('withdrawForm').addEventListener('submit', (e) => submitForm(e, 'http://localhost:8000/api/withdraw'));
    document.getElementById('transferForm').addEventListener('submit', (e) => submitForm(e, 'http://localhost:8000/api/transfer'));
    document.getElementById('balanceForm').addEventListener('submit', (e) => submitForm(e, `http://localhost:8000/api/balance/${e.target.user_id.value}`));
</script>
</body>
</html>
