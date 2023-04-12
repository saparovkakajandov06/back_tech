<!DOCTYPE html>
<html lang="ru-RU">

<head>
    <meta charset="utf-8">
</head>

<body>
    <h2>Скрейпер: {{ $alias }}, код ответа: {{ $responseCode }}</h2>
    <div>
        <p>Хост: <a href="{{ $host }}">{{ $host }}</a></p>
        <p>Ендпоинт: <b>{{ $requestUrl }}</b></p>
        <p>Параметры: <b>{{ $requestParams }}</b></p>
        <p>Ответ:
        <pre>{{ $responseBody }}</pre>
        </p>
        <p>Лимиты: <b>{{ $limits }}</b></p>
        <p>Дата: <b>{{ $date }}</b></p>
    </div>
</body>

</html>