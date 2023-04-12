<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Facebook Pixel error</h2>

<div>
    <p>Запрос:</p>
    <pre> {{ json_encode($requestData, JSON_PRETTY_PRINT) }}</pre>
    <br>
    <br>
    <p>Ответ:</p>
    <pre> {{ json_encode($responseData, JSON_PRETTY_PRINT) }}</pre>
    <br>
    <br>
    <p>Дата: <b>{{ now()->format('d.m.Y') }}</b></p>
</div>

</body>
</html>
