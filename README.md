Основной функционал
1. Начисление средств пользователю
   POST /api/deposit

{
"user_id": 1,
"amount": 500.00,
"comment": "Пополнение через карту"
}
2. Списание средств
   POST /api/withdraw

{
"user_id": 1,
"amount": 200.00,
"comment": "Покупка подписки"
}
Баланс не может уходить в минус.

3. Перевод между пользователями
   POST /api/transfer

{
"from_user_id": 1,
"to_user_id": 2,
"amount": 150.00,
"comment": "Перевод другу"
}
4. Получение баланса пользователя
   GET /api/balance/{user_id}

{
"user_id": 1,
"balance": 350.00
}
Требования
PHP 8+
PostgreSQL для хранения данных.
Приложение развернуто в Docker.
Все денежные операции выполняются в транзакциях.
Баланс не может быть отрицательным.
Если у пользователя нет записи о балансе, она создаётся при первом пополнении.
Все ответы и ошибки в формате JSON с корректными HTTP-кодами:
200 — успешный ответ.
400 / 422 — ошибки валидации.
404 — пользователь не найден.
409 — конфликт (например, недостаточно средств).
Установка и запуск
Требования
Docker и Docker Compose.
Шаги
Клонируйте репозиторий:

git clone <ваш репозиторий>
cd <имя папки проекта>
Создайте .env файл на основе примера:

cp .env.example .env
Настройте переменные окружения в .env (например, DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

Соберите и запустите контейнеры:

docker-compose up --build -d
Установите зависимости Laravel:

docker-compose exec app composer install
Выполните миграции:

docker-compose exec app php artisan migrate
Запустите тесты (опционально):

docker-compose exec app php artisan test
Приложение будет доступно по адресу: http://localhost:8000.

Использование API
Примеры запросов с использованием curl:

1. Начисление средств
   curl -X POST http://localhost:8000/api/deposit \
   -H "Content-Type: application/json" \
   -d '{"user_id": 1, "amount": 500.00, "comment": "Пополнение через карту"}'
2. Списание средств
   curl -X POST http://localhost:8000/api/withdraw \
   -H "Content-Type: application/json" \
   -d '{"user_id": 1, "amount": 200.00, "comment": "Покупка подписки"}'
3. Перевод средств
   curl -X POST http://localhost:8000/api/transfer \
   -H "Content-Type: application/json" \
   -d '{"from_user_id": 1, "to_user_id": 2, "amount": 150.00, "comment": "Перевод другу"}'
4. Получение баланса
   curl -X GET http://localhost:8000/api/balance/1
   Тестирование
   Тесты написаны с использованием PHPUnit. Чтобы запустить тесты, выполните:

docker-compose exec app php artisan test
Структура базы данных
Основные таблицы
users — пользователи.
wallets — кошельки пользователей.
wallet_transactions — транзакции.
