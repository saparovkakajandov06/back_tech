---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://smmtouch.store/docs/collection.json)

<!-- END_INFO -->

#auth


Контроллер для аутентификации пользователей
<!-- START_7fcb5680ca155cd44e35b64c867ca4f3 -->
## Local login
Локальный логин: login/email + password

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/login/local" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"login":"omnis","email":"nobis","password":"maiores"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/login/local"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "login": "omnis",
    "email": "nobis",
    "password": "maiores"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "token": "random_string"
}
```

### HTTP Request
`POST api/login/local`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `login` | string |  optional  | имя пользователя
        `email` | string |  optional  | email
        `password` | string |  required  | пароль
    
<!-- END_7fcb5680ca155cd44e35b64c867ca4f3 -->

<!-- START_4f05517bb8d3f0c6f92dc8cc2c9e5b03 -->
## uLogin by token
Логин через социальную сеть с помощью токена uLogin
Возвращает токен либо массив данных для уточнения email

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/login/ulogin/token" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"host":"architecto","token":"excepturi","ref_code":"nisi"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/login/ulogin/token"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "host": "architecto",
    "token": "excepturi",
    "ref_code": "nisi"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "token": "random_string"
}
```
> Example response (200):

```json
{
    "data": []
}
```

### HTTP Request
`POST api/login/ulogin/token`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `host` | required |  optional  | ulogin host
        `token` | required |  optional  | ulogin token
        `ref_code` | реферальный |  optional  | код
    
<!-- END_4f05517bb8d3f0c6f92dc8cc2c9e5b03 -->

<!-- START_3115fb7f230c4d3d9d9e86b6267005d4 -->
## uLogin by data
Логин через uLogin с помощью массива данных о пользователе

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/login/ulogin/data" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"data":[],"ref_code":"qui"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/login/ulogin/data"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "data": [],
    "ref_code": "qui"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "token": "random_string"
}
```

### HTTP Request
`POST api/login/ulogin/data`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `data` | array |  optional  | массив данных о пользователе социальной сети с ulogin
        `ref_code` | реферальный |  optional  | код
    
<!-- END_3115fb7f230c4d3d9d9e86b6267005d4 -->

<!-- START_d7b7952e7fdddc07c978c9bdaf757acf -->
## Register new user
Регистрация нового пользователя

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/register" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"login":"est","email":"libero","password":"omnis","password_confirm":"error","ref_code":"soluta"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/register"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "login": "est",
    "email": "libero",
    "password": "omnis",
    "password_confirm": "error",
    "ref_code": "soluta"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "success",
    "message": "ok",
    "data": {
        "token": "random_string"
    }
}
```

### HTTP Request
`POST api/register`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `login` | string |  required  | логин
        `email` | string |  required  | email
        `password` | string |  required  | пароль
        `password_confirm` | string |  required  | подтверждение пароля
        `ref_code` | string |  optional  | реферальный код
    
<!-- END_d7b7952e7fdddc07c978c9bdaf757acf -->

<!-- START_f370089530708a564e506ed6074a9d46 -->
## Verify registration
Подтверждает регистрацию нового пользователя
В случае успеха устанавливает пользователю
группу ROLE_VERIFIED.

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/confirm/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/confirm/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\app\\Exceptions\\APIException.php",
        "line": 21,
        "message": "",
        "class": "App\\Exceptions\\InvalidCodeException"
    }
}
```

### HTTP Request
`GET api/confirm/{confirmationCode}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `confirmation_code` |  optional  | string required код подтверждения

<!-- END_f370089530708a564e506ed6074a9d46 -->

<!-- START_e1a99444c6d02f7d3fc5b451bb44be1d -->
## Reset password
Отправляет пользователю письмо с кодом для смены пароля

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/reset" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email":"nisi"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/reset"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "nisi"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/reset`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `email` | string |  required  | email пользователя
    
<!-- END_e1a99444c6d02f7d3fc5b451bb44be1d -->

<!-- START_66d1e8e24501aad0fd10bf04065dde9a -->
## Set password
Устанавливает новый пароль по коду из письма

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/set_password" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"reset_code":"vero","password":"qui","password_confirm":"velit"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/set_password"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "reset_code": "vero",
    "password": "qui",
    "password_confirm": "velit"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/set_password`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `reset_code` | string |  required  | код из письма
        `password` | string |  required  | новый пароль
        `password_confirm` | string |  required  | подтверждение пароля
    
<!-- END_66d1e8e24501aad0fd10bf04065dde9a -->

<!-- START_61739f3220a224b34228600649230ad1 -->
## Log out
Выйти из системы

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/logout" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/logout"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "message": "Successfully logged out"
}
```

### HTTP Request
`POST api/logout`


<!-- END_61739f3220a224b34228600649230ad1 -->

<!-- START_d2a8dfe2d9c2f49a66662569f45711bc -->
## Update password
Обновить пароль

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/update_password" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"old_password":"dolor","password":"accusantium","password_confirm":"accusantium"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/update_password"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "old_password": "dolor",
    "password": "accusantium",
    "password_confirm": "accusantium"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "success": "Password changed."
}
```

### HTTP Request
`POST api/update_password`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `old_password` | string |  required  | старый пароль
        `password` | string |  required  | новый пароль
        `password_confirm` | string |  required  | подтверждение пароля
    
<!-- END_d2a8dfe2d9c2f49a66662569f45711bc -->

<!-- START_ce0b2ab0486426336db6c8493bdf75fa -->
## Update avatar
Обновить аватар

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/update_avatar" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"avatar":"eum"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/update_avatar"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "avatar": "eum"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "success": "avatar changed"
}
```

### HTTP Request
`POST api/update_avatar`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `avatar` | required |  optional  | файл аватара mimes: jpeg, jpg, png
    
<!-- END_ce0b2ab0486426336db6c8493bdf75fa -->

#config


<!-- START_7177ba0ebac2272054e0aa03a71ad4c0 -->
## Get config value
Админ: Получить данные из конфига

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/config/eos" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/config/eos"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php",
        "line": 82,
        "message": "Unauthenticated.",
        "class": "Illuminate\\Auth\\AuthenticationException"
    }
}
```

### HTTP Request
`GET api/config/{tag}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `tag` |  optional  | string required тег

<!-- END_7177ba0ebac2272054e0aa03a71ad4c0 -->

<!-- START_5fe1ef5218e7e45315d137a9b5b27a4f -->
## Set config value
Админ: Установить значение в конфиг
Все поля из запроса будут вмержены в конфиг по тегу

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/config/tempore" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/config/tempore"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/config/{tag}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `tag` |  optional  | string required тег

<!-- END_5fe1ef5218e7e45315d137a9b5b27a4f -->

#general


<!-- START_cd4a874127cd23508641c63b640ee838 -->
## doc.json
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/doc.json" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/doc.json"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "variables": [],
    "info": {
        "name": "Laravel API",
        "_postman_id": "c582b91d-df9f-4d65-bf33-b97fc461f469",
        "description": "",
        "schema": "https:\/\/schema.getpostman.com\/json\/collection\/v2.0.0\/collection.json"
    },
    "item": [
        {
            "name": "auth",
            "description": "\nКонтроллер для аутентификации пользователей",
            "item": [
                {
                    "name": "Local login\nЛокальный логин: login\/email + password",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/login\/local",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"login\": \"vel\",\n    \"email\": \"quasi\",\n    \"password\": \"voluptas\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "uLogin by token\nЛогин через социальную сеть с помощью токена uLogin\nВозвращает токен либо массив данных для уточнения email",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/login\/ulogin\/token",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"host\": \"enim\",\n    \"token\": \"sit\",\n    \"ref_code\": \"quia\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "uLogin by data\nЛогин через uLogin с помощью массива данных о пользователе",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/login\/ulogin\/data",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"data\": [],\n    \"ref_code\": \"quibusdam\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Update password\nОбновить пароль",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/update_password",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"old_password\": \"est\",\n    \"password\": \"quo\",\n    \"password_confirm\": \"placeat\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Update avatar\nОбновить аватар",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/update_avatar",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"avatar\": \"itaque\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Register new user\nРегистрация нового пользователя",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/register",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"login\": \"aliquid\",\n    \"email\": \"aut\",\n    \"password\": \"ut\",\n    \"password_confirm\": \"voluptatem\",\n    \"ref_code\": \"quis\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Verify registration\nПодтверждает регистрацию нового пользователя\nВ случае успеха устанавливает пользователю\nгруппу ROLE_VERIFIED.",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/confirm\/:confirmationCode",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Reset password\nОтправляет пользователю письмо с кодом для смены пароля",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/reset",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"email\": \"incidunt\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Set password\nУстанавливает новый пароль по коду из письма",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/set_password",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"reset_code\": \"quia\",\n    \"password\": \"quibusdam\",\n    \"password_confirm\": \"voluptas\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Log out\nВыйти из системы",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/logout",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                }
            ]
        },
        {
            "name": "general",
            "description": "",
            "item": [
                {
                    "name": "doc.json",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "doc.json",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/posts",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/posts",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/posts",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/posts",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/posts\/{post}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/posts\/:post",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/posts\/{post}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/posts\/:post",
                            "query": []
                        },
                        "method": "PUT",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/posts\/{post}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/posts\/:post",
                            "query": []
                        },
                        "method": "DELETE",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/posts\/slug\/{slug}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/posts\/slug\/:slug",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/notifications",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/notifications",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/notifications",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/notifications",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/notifications\/{notification}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/notifications\/:notification",
                            "query": []
                        },
                        "method": "PUT",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/notifications\/{notification}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/notifications\/:notification",
                            "query": []
                        },
                        "method": "DELETE",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/prices",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/prices",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/prices",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/prices",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/prices\/{price}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/prices\/:price",
                            "query": []
                        },
                        "method": "PUT",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/prices\/{price}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/prices\/:price",
                            "query": []
                        },
                        "method": "DELETE",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/config\/{tag}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/config\/:tag",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/config\/{tag}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/config\/:tag",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                }
            ]
        },
        {
            "name": "orders",
            "description": "",
            "item": [
                {
                    "name": "Orders for user",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/admin\/composite-orders",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"name\": \"doloribus\"\n}"
                        },
                        "description": "Администратор: Список заказов пользователя",
                        "response": []
                    }
                },
                {
                    "name": "Create order from main page\nДля создания заказов с главной страницы\nМожет принимать любые параметры, например link, count и т.д.",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/c_orders\/main",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"tag\": \"et\",\n    \"return_url\": \"tenetur\"\n}"
                        },
                        "description": "Набор параметров зависит от UserService",
                        "response": []
                    }
                },
                {
                    "name": "List of composite orders for current user\nЗаказы текущего пользователя",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/c_orders",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Create composite order\nДля создания заказов в ЛК\nНабор параметров зависит от сервиса",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/c_orders",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"tag\": \"delectus\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/c_orders\/{id}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/c_orders\/:id",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                }
            ]
        },
        {
            "name": "payments",
            "description": "",
            "item": [
                {
                    "name": "Hook for yandex\nСюда стучится яндекс-касса",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/yk_status",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Deposit with yandex\nПополнить счет через яндекс-кассу",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/yk_pay",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"amount\": 4,\n    \"return_url\": \"officia\"\n}"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/withdrawal",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/withdrawal",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                }
            ]
        },
        {
            "name": "premium_statuses",
            "description": "\nПрограмма лояльности",
            "item": [
                {
                    "name": "Список статусов для программы лояльности",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/premium_statuses",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/premium_statuses_update",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/premium_statuses_update",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                }
            ]
        },
        {
            "name": "testing",
            "description": "\nКонтроллер для проверки авторизации",
            "item": [
                {
                    "name": "API ping\nПинг апи",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/ping",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Test auth\nПроверка авторизации",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/test_auth",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Test moderator\nПроверка прав доступа для модератора",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/test_moderator",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Test admin\nПроверка прав доступа для администратора",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/test_admin",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                }
            ]
        },
        {
            "name": "user_services",
            "description": "",
            "item": [
                {
                    "name": "User services index\nСписок пользовательских сервисов, у которых visible=1",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/user_services",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "User services for main\nСписок пользовательских сервисов, у которых visible=1, main=1",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/user_services\/main",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "User services by group\nСписок пользовательских сервисов в группе, у которых visible=1",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/user_services\/show_group\/:show_group",
                            "query": [],
                            "variable": [
                                {
                                    "id": "show_group",
                                    "key": "show_group",
                                    "value": "libero",
                                    "description": "string required группа"
                                }
                            ]
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "User service by tag\nПоиск пользовательского сервиса по тегу",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/user_services\/:tag",
                            "query": [],
                            "variable": [
                                {
                                    "id": "tag",
                                    "key": "tag",
                                    "value": "necessitatibus",
                                    "description": "string required уникальный тег сервиса"
                                }
                            ]
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/user_services\/{tag}",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/user_services\/:tag",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                }
            ]
        },
        {
            "name": "users",
            "description": "",
            "item": [
                {
                    "name": "Find user by name or email",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/admin\/users",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "{\n    \"name\": \"mollitia\",\n    \"email\": \"sequi\"\n}"
                        },
                        "description": "Администратор: поиск пользователя по имени или email",
                        "response": []
                    }
                },
                {
                    "name": "User details\nИнформация о текущем пользователе",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/user",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "Update user data\nОбновить данные пользователя, кроме пароля и аватара\nЗаписывает все поля запроса в текущего пользователя",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/user",
                            "query": []
                        },
                        "method": "POST",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                },
                {
                    "name": "api\/users",
                    "request": {
                        "url": {
                            "protocol": "http",
                            "host": "smmtouch.store",
                            "path": "api\/users",
                            "query": []
                        },
                        "method": "GET",
                        "header": [
                            {
                                "key": "Content-Type",
                                "value": "application\/json"
                            },
                            {
                                "key": "Accept",
                                "value": "application\/json"
                            }
                        ],
                        "body": {
                            "mode": "raw",
                            "raw": "[]"
                        },
                        "description": "",
                        "response": []
                    }
                }
            ]
        }
    ]
}
```

### HTTP Request
`GET doc.json`


<!-- END_cd4a874127cd23508641c63b640ee838 -->

<!-- START_da50450f1df5336c2a14a7a368c5fb9c -->
## api/posts
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/posts" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/posts"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
[
    {
        "id": 1,
        "heading": "Mrs.",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Architecto omnis consectetur ut. Sed aliquid ratione laboriosam sed mollitia dolor suscipit. Qui rerum quisquam est deserunt facilis consectetur quos.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    },
    {
        "id": 2,
        "heading": "Mrs.",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Sit voluptates facilis assumenda maiores debitis vero quia voluptates. Magnam voluptates et non possimus.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    },
    {
        "id": 3,
        "heading": "Prof.",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Sed et quas veritatis minus consectetur. Provident distinctio officia ipsum architecto. Est quis ratione iusto vitae aperiam placeat ducimus.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    },
    {
        "id": 4,
        "heading": "Prof.",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Distinctio non sunt error sed tempora quia. Ut quas non dignissimos rerum non. Et dolorem consequatur consectetur eligendi sit quia. Nisi sunt sapiente ut.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    },
    {
        "id": 5,
        "heading": "Prof.",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Tempore consequatur consequatur qui perspiciatis natus. Facere quae harum ipsum sequi rem. Consectetur quia repudiandae quo vel aspernatur quia et. Earum magnam quibusdam rerum asperiores.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    },
    {
        "id": 6,
        "heading": "Dr.",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Illum velit sunt qui totam est. Aperiam consectetur doloremque tempora quasi. Maiores repellat quos consequuntur consequatur qui consectetur corporis reprehenderit.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    },
    {
        "id": 7,
        "heading": "Dr.",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Eos officiis dolor cum et itaque consequatur. Aut eligendi sit ex aut necessitatibus nulla. Ut qui qui provident culpa.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    },
    {
        "id": 8,
        "heading": "Prof.",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Occaecati ut rerum culpa et ab cumque. Facere eaque recusandae et ratione ex iure vitae. Perferendis dicta aspernatur rerum doloribus veritatis vel sit. Hic hic et et odio non corrupti veniam eaque.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    },
    {
        "id": 9,
        "heading": "Miss",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Quibusdam ipsa laboriosam numquam unde modi. Eum autem sed voluptate quia fuga. Qui nisi modi facilis a quo. Eligendi minima possimus qui voluptas aut numquam.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    },
    {
        "id": 10,
        "heading": "Dr.",
        "description": null,
        "views": 0,
        "slug": null,
        "cover": null,
        "date": null,
        "image": null,
        "body": "Et facere aperiam autem id eaque voluptate voluptatibus quis. Laboriosam et et debitis quia minus beatae. Ut est suscipit earum vel nobis.",
        "created_at": "2020-05-05T20:27:05.000000Z",
        "updated_at": "2020-05-05T20:27:05.000000Z"
    }
]
```

### HTTP Request
`GET api/posts`


<!-- END_da50450f1df5336c2a14a7a368c5fb9c -->

<!-- START_ea8d166c68ec035668ea724e12cafa45 -->
## api/posts
> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/posts" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/posts"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/posts`


<!-- END_ea8d166c68ec035668ea724e12cafa45 -->

<!-- START_726b7bf93b3209836a1cbcda5b3b6703 -->
## api/posts/{post}
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/posts/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/posts/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "id": 1,
    "heading": "Mrs.",
    "description": null,
    "views": 0,
    "slug": null,
    "cover": null,
    "date": null,
    "image": null,
    "body": "Architecto omnis consectetur ut. Sed aliquid ratione laboriosam sed mollitia dolor suscipit. Qui rerum quisquam est deserunt facilis consectetur quos.",
    "created_at": "2020-05-05T20:27:05.000000Z",
    "updated_at": "2020-05-05T20:27:05.000000Z"
}
```

### HTTP Request
`GET api/posts/{post}`


<!-- END_726b7bf93b3209836a1cbcda5b3b6703 -->

<!-- START_6d1dfaf5fa710725519375063e4e9db0 -->
## api/posts/{post}
> Example request:

```bash
curl -X PUT \
    "http://smmtouch.store/api/posts/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/posts/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/posts/{post}`

`PATCH api/posts/{post}`


<!-- END_6d1dfaf5fa710725519375063e4e9db0 -->

<!-- START_790d23dbb8c799c36c70f7133a51e7a5 -->
## api/posts/{post}
> Example request:

```bash
curl -X DELETE \
    "http://smmtouch.store/api/posts/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/posts/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/posts/{post}`


<!-- END_790d23dbb8c799c36c70f7133a51e7a5 -->

<!-- START_bf1ae5c7fc2780182e4908d845587862 -->
## api/posts/slug/{slug}
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/posts/slug/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/posts/slug/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`GET api/posts/slug/{slug}`


<!-- END_bf1ae5c7fc2780182e4908d845587862 -->

<!-- START_e65df2963c4f1f0bfdd426ee5170e8b7 -->
## api/notifications
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/notifications" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/notifications"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php",
        "line": 82,
        "message": "Unauthenticated.",
        "class": "Illuminate\\Auth\\AuthenticationException"
    }
}
```

### HTTP Request
`GET api/notifications`


<!-- END_e65df2963c4f1f0bfdd426ee5170e8b7 -->

<!-- START_e3e4b7925cb9217b5886830dd505827e -->
## api/notifications
> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/notifications" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/notifications"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/notifications`


<!-- END_e3e4b7925cb9217b5886830dd505827e -->

<!-- START_925ff04a7e2ea910accc06ad080a3503 -->
## api/notifications/{notification}
> Example request:

```bash
curl -X PUT \
    "http://smmtouch.store/api/notifications/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/notifications/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/notifications/{notification}`

`PATCH api/notifications/{notification}`


<!-- END_925ff04a7e2ea910accc06ad080a3503 -->

<!-- START_d1aee9dd60b3caeea32a2ad84cc15e48 -->
## api/notifications/{notification}
> Example request:

```bash
curl -X DELETE \
    "http://smmtouch.store/api/notifications/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/notifications/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/notifications/{notification}`


<!-- END_d1aee9dd60b3caeea32a2ad84cc15e48 -->

<!-- START_5f545593f6b2f5f1077312e16c775afc -->
## api/prices
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/prices" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/prices"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php",
        "line": 82,
        "message": "Unauthenticated.",
        "class": "Illuminate\\Auth\\AuthenticationException"
    }
}
```

### HTTP Request
`GET api/prices`


<!-- END_5f545593f6b2f5f1077312e16c775afc -->

<!-- START_7509554f83fee791e85b40eceb5b0075 -->
## api/prices
> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/prices" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/prices"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/prices`


<!-- END_7509554f83fee791e85b40eceb5b0075 -->

<!-- START_2e59b4c5fc1429604aa6b6dc1337a490 -->
## api/prices/{price}
> Example request:

```bash
curl -X PUT \
    "http://smmtouch.store/api/prices/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/prices/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT api/prices/{price}`

`PATCH api/prices/{price}`


<!-- END_2e59b4c5fc1429604aa6b6dc1337a490 -->

<!-- START_ba5da554ce149fc76b1fe3c8b23e64b9 -->
## api/prices/{price}
> Example request:

```bash
curl -X DELETE \
    "http://smmtouch.store/api/prices/1" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/prices/1"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE api/prices/{price}`


<!-- END_ba5da554ce149fc76b1fe3c8b23e64b9 -->

#orders


<!-- START_08ec41cd9676fe44b31c612f88d5ba2f -->
## Orders for user

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
Администратор: Список заказов пользователя

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/admin/composite-orders" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"name":"doloribus"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/admin/composite-orders"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "doloribus"
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php",
        "line": 82,
        "message": "Unauthenticated.",
        "class": "Illuminate\\Auth\\AuthenticationException"
    }
}
```

### HTTP Request
`GET api/admin/composite-orders`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `name` | string |  required  | 
    
<!-- END_08ec41cd9676fe44b31c612f88d5ba2f -->

<!-- START_d0d21cc34e13d292e912f69d03915ae5 -->
## Create order from main page
Для создания заказов с главной страницы
Может принимать любые параметры, например link, count и т.д.

Набор параметров зависит от UserService

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/c_orders/main" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"tag":"tenetur","return_url":"cumque"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/c_orders/main"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "tag": "tenetur",
    "return_url": "cumque"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/c_orders/main`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `tag` | string |  required  | тег сервиса
        `return_url` | string |  required  | return_url для возврата после оплаты
    
<!-- END_d0d21cc34e13d292e912f69d03915ae5 -->

<!-- START_cb77029bfa39edf22ece2473f07f8892 -->
## List of composite orders for current user
Заказы текущего пользователя

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/c_orders" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/c_orders"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php",
        "line": 82,
        "message": "Unauthenticated.",
        "class": "Illuminate\\Auth\\AuthenticationException"
    }
}
```

### HTTP Request
`GET api/c_orders`


<!-- END_cb77029bfa39edf22ece2473f07f8892 -->

<!-- START_7ceabb742a43b7d94abd49b2a28944f4 -->
## Create composite order
Для создания заказов в ЛК
Набор параметров зависит от сервиса

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/c_orders" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"tag":"voluptatem"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/c_orders"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "tag": "voluptatem"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/c_orders`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `tag` | string |  required  | уникальный тег сервиса
    
<!-- END_7ceabb742a43b7d94abd49b2a28944f4 -->

<!-- START_25c7cba088d4fe7fb2337caa9b0006b6 -->
## Show order
Показывает заказ

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/c_orders/sint" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/c_orders/sint"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php",
        "line": 82,
        "message": "Unauthenticated.",
        "class": "Illuminate\\Auth\\AuthenticationException"
    }
}
```

### HTTP Request
`GET api/c_orders/{id}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `id` |  optional  | integer required id заказа

<!-- END_25c7cba088d4fe7fb2337caa9b0006b6 -->

#payments


<!-- START_c64e1871d41c0ba1b47822f5de447734 -->
## Hook for yandex
Сюда стучится яндекс-касса

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/yk_status" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/yk_status"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/yk_status`


<!-- END_c64e1871d41c0ba1b47822f5de447734 -->

<!-- START_eff4bb1ba838bfaf1039de315445f5d0 -->
## Deposit with yandex
Пополнить счет через яндекс-кассу

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/yk_pay" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"amount":2,"return_url":"nihil"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/yk_pay"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "amount": 2,
    "return_url": "nihil"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/yk_pay`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `amount` | integer |  required  | сумма
        `return_url` | string |  required  | url для возврата
    
<!-- END_eff4bb1ba838bfaf1039de315445f5d0 -->

<!-- START_0380e94584fb559e8ebc418f3a9b01cd -->
## Withdraw money
Вывод денег

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/withdrawal" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"amount":17,"withdraw_method":5,"wallet_number":11}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/withdrawal"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "amount": 17,
    "withdraw_method": 5,
    "wallet_number": 11
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "success": true,
    "balance": 123
}
```

### HTTP Request
`POST api/withdrawal`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `amount` | integer |  required  | сумма
        `withdraw_method` | integer |  required  | код метода
        `wallet_number` | integer |  required  | номер кошелька
    
<!-- END_0380e94584fb559e8ebc418f3a9b01cd -->

#premium_statuses


Программа лояльности
<!-- START_644226e04ecef612b0b757fc260ac047 -->
## Список статусов для программы лояльности

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/premium_statuses" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/premium_statuses"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "success",
    "message": "ok",
    "data": []
}
```

### HTTP Request
`GET api/premium_statuses`


<!-- END_644226e04ecef612b0b757fc260ac047 -->

<!-- START_b4440e251d2869ab8c0366c3e5de4811 -->
## Update PremiumStatus
Изменение статуса в программе лояльности

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/premium_statuses_update" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/premium_statuses_update"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/premium_statuses_update`


<!-- END_b4440e251d2869ab8c0366c3e5de4811 -->

#testing


Контроллер для проверки авторизации
<!-- START_be144776226f630c3f444c294d8a0395 -->
## API ping
Пинг апи

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/ping" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/ping"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "success",
    "message": "pong",
    "data": []
}
```

### HTTP Request
`GET api/ping`


<!-- END_be144776226f630c3f444c294d8a0395 -->

<!-- START_744061ac07720b47cdfc21af5b880352 -->
## Test auth
Проверка авторизации

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/test_auth" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/test_auth"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "success",
    "message": "test auth",
    "data": []
}
```

### HTTP Request
`GET api/test_auth`

`POST api/test_auth`

`PUT api/test_auth`

`PATCH api/test_auth`

`DELETE api/test_auth`

`OPTIONS api/test_auth`


<!-- END_744061ac07720b47cdfc21af5b880352 -->

<!-- START_3f43f4ed68fd946bd88824f05db7c28d -->
## Test moderator
Проверка прав доступа для модератора

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/test_moderator" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/test_moderator"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "success",
    "message": "test moderator",
    "data": []
}
```

### HTTP Request
`GET api/test_moderator`

`POST api/test_moderator`

`PUT api/test_moderator`

`PATCH api/test_moderator`

`DELETE api/test_moderator`

`OPTIONS api/test_moderator`


<!-- END_3f43f4ed68fd946bd88824f05db7c28d -->

<!-- START_13ee152605aa8da5ae34cb7ed5e4237f -->
## Test admin
Проверка прав доступа для администратора

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/test_admin" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/test_admin"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "success",
    "message": "test admin",
    "data": []
}
```

### HTTP Request
`GET api/test_admin`

`POST api/test_admin`

`PUT api/test_admin`

`PATCH api/test_admin`

`DELETE api/test_admin`

`OPTIONS api/test_admin`


<!-- END_13ee152605aa8da5ae34cb7ed5e4237f -->

#user_services


<!-- START_264edcc5117b6d97de111ef5f546cb45 -->
## User services index
Список пользовательских сервисов, у которых visible=1

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/user_services" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/user_services"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "success",
    "message": "Пользовательские сервисы",
    "data": [
        {
            "id": 1,
            "title": "С гарантией - 45 руб",
            "tag": "INSTAGRAM_SUBS_LK",
            "show_group": "GROUP_SUBS",
            "pay_group": "GROUP_SUBS",
            "price_list": {
                "1": 0.45,
                "1000": 0.44,
                "5000": 0.43,
                "10000": 0.41,
                "25000": 0.42,
                "50000": 0.394,
                "100000": 0.39
            },
            "img": "\/svg\/subs.svg",
            "description": {
                "startup": "Мгновенно",
                "speed": "10000-20000 в сутки",
                "min": "100",
                "max": "40000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "С аватарками-публикациями. Боты и офферы. В случае уменьшения заказанного количества подписчики автоматически восстанавливаются в течение 1-3 дней. Автовосстановление действует в течение 30 дней."
            },
            "card": null,
            "main": 0
        },
        {
            "id": 2,
            "title": "Быстрые лайки - 19 руб",
            "tag": "INSTAGRAM_LIKES_LK",
            "show_group": "GROUP_LIKES",
            "pay_group": "GROUP_LIKES",
            "price_list": {
                "1": 0.19,
                "1000": 0.185,
                "5000": 0.182,
                "10000": 0.179,
                "25000": 0.178,
                "50000": 0.175,
                "100000": 0.175
            },
            "img": "\/svg\/like.svg",
            "description": {
                "startup": "В течение 1-5 минут",
                "speed": "300 - 500 в минуту",
                "min": "100",
                "max": "10000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "Лайкают качественные офферы. Охват повышает статистику страницы.Идеально подходят для вывода в топ.Моментальный запуск."
            },
            "card": null,
            "main": 0
        },
        {
            "id": 3,
            "title": "Быстрые лайки - 19 руб",
            "tag": "INSTAGRAM_MULTI_LIKES_LK",
            "show_group": "GROUP_OTHER",
            "pay_group": "GROUP_LIKES",
            "price_list": {
                "1": 0.05,
                "1000": 0.045,
                "5000": 0.043,
                "10000": 0.04,
                "25000": 0.04,
                "50000": 0.037,
                "100000": 0.035
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "В течение 1-5 минут",
                "speed": "300 - 500 в минуту",
                "min": "100",
                "max": "10000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "Лайкают качественные офферы. Охват повышает статистику страницы.Идеально подходят для вывода в топ.Моментальный запуск."
            },
            "card": null,
            "main": 0
        },
        {
            "id": 4,
            "title": "В прямой эфир - 7 руб",
            "tag": "INSTAGRAM_LIKES_LIVE_LK",
            "show_group": "GROUP_LIKES",
            "pay_group": "GROUP_LIKES",
            "price_list": {
                "1": 0.19,
                "1000": 0.185,
                "5000": 0.182,
                "10000": 0.179,
                "25000": 0.178,
                "50000": 0.175,
                "100000": 0.175
            },
            "img": "\/svg\/like.svg",
            "description": {
                "startup": "В течение 1-5 минут",
                "speed": "300 - 500 в минуту",
                "min": "100",
                "max": "10000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "Лайкают качественные офферы. Охват повышает статистику страницы.Идеально подходят для вывода в топ.Моментальный запуск."
            },
            "card": null,
            "main": 0
        },
        {
            "id": 5,
            "title": "Видео - 5 руб",
            "tag": "INSTAGRAM_VIEWS_VIDEO_IMPRESSIONS_LK",
            "show_group": "GROUP_VIEWS",
            "pay_group": "GROUP_VIEWS",
            "price_list": {
                "1": 0.05,
                "1000": 0.045,
                "5000": 0.043,
                "10000": 0.04,
                "25000": 0.04,
                "50000": 0.037,
                "100000": 0.035
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "В течение 5-20 минут",
                "speed": "20000 в сутки",
                "min": "5000",
                "max": "1000000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "Офферы со всего мира. Внимание! Статистика в инстаграм обновляется с задержкой"
            },
            "card": null,
            "main": 0
        },
        {
            "id": 6,
            "title": "IGTV - 5 руб",
            "tag": "INSTAGRAM_VIEWS_IGTV_LK",
            "show_group": "GROUP_VIEWS",
            "pay_group": "GROUP_VIEWS",
            "price_list": {
                "1": 0.05,
                "1000": 0.045,
                "5000": 0.043,
                "10000": 0.04,
                "25000": 0.04,
                "50000": 0.037,
                "100000": 0.035
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "В течение 5-20 минут",
                "speed": "20000 в сутки",
                "min": "5000",
                "max": "1000000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "Офферы со всего мира. Внимание! Статистика в инстаграм обновляется с задержкой"
            },
            "card": null,
            "main": 0
        },
        {
            "id": 7,
            "title": "Story - 7 руб",
            "tag": "INSTAGRAM_VIEWS_STORY_LK",
            "show_group": "GROUP_VIEWS",
            "pay_group": "GROUP_VIEWS",
            "price_list": {
                "1": 0.07,
                "1000": 0.07,
                "5000": 0.07,
                "10000": 0.07,
                "25000": 0.07,
                "50000": 0.07,
                "100000": 0.07
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "В течение 5 минут",
                "speed": "2000-10000 в час",
                "min": "100",
                "max": "1000000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "Указывать только логин!"
            },
            "card": null,
            "main": 0
        },
        {
            "id": 8,
            "title": "Показы + Охват - 5 руб",
            "tag": "INSTAGRAM_VIEWS_SHOW_IMPRESSIONS_LK",
            "show_group": "GROUP_VIEWS",
            "pay_group": "GROUP_VIEWS",
            "price_list": {
                "1": 0.05,
                "1000": 0.045,
                "5000": 0.043,
                "10000": 0.04,
                "25000": 0.04,
                "50000": 0.037,
                "100000": 0.035
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "В течение 5-20 минут",
                "speed": "20000 в сутки",
                "min": "5000",
                "max": "1000000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "Офферы со всего мира. Внимание! Статистика в инстаграм обновляется с задержкой"
            },
            "card": null,
            "main": 0
        },
        {
            "id": 9,
            "title": "Зрители в прямой эфир - 70 руб",
            "tag": "INSTAGRAM_VIEWS_VIEWERS_LIVE_LK",
            "show_group": "GROUP_VIEWS",
            "pay_group": "GROUP_VIEWS",
            "price_list": {
                "1": 0.05,
                "1000": 0.045,
                "5000": 0.043,
                "10000": 0.04,
                "25000": 0.04,
                "50000": 0.037,
                "100000": 0.035
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "В течение 5-20 минут",
                "speed": "20000 в сутки",
                "min": "5000",
                "max": "1000000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "Офферы со всего мира. Внимание! Статистика в инстаграм обновляется с задержкой"
            },
            "card": null,
            "main": 0
        },
        {
            "id": 10,
            "title": "Хорошие комментарии",
            "tag": "INSTAGRAM_COMMENTS_LK",
            "show_group": "GROUP_COMMENTS",
            "pay_group": "GROUP_COMMENTS",
            "price_list": {
                "1": 0.05,
                "1000": 0.045,
                "5000": 0.043,
                "10000": 0.04,
                "25000": 0.04,
                "50000": 0.037,
                "100000": 0.035
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "",
                "speed": "",
                "min": "",
                "max": "",
                "requirements": "",
                "details": ""
            },
            "card": null,
            "main": 0
        },
        {
            "id": 11,
            "title": "На последние посты",
            "tag": "INSTAGRAM_MULTI_COMMENTS_LK",
            "show_group": "GROUP_COMMENTS",
            "pay_group": "GROUP_COMMENTS",
            "price_list": {
                "1": 0.05,
                "1000": 0.045,
                "5000": 0.043,
                "10000": 0.04,
                "25000": 0.04,
                "50000": 0.037,
                "100000": 0.035
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "",
                "speed": "",
                "min": "",
                "max": "",
                "requirements": "",
                "details": ""
            },
            "card": null,
            "main": 0
        },
        {
            "id": 12,
            "title": "Автолайки - 19 руб",
            "tag": "INSTAGRAM_AUTO_LIKES_LK",
            "show_group": "GROUP_AUTO",
            "pay_group": "GROUP_LIKES",
            "price_list": {
                "1": 0.05,
                "1000": 0.045,
                "5000": 0.043,
                "10000": 0.04,
                "25000": 0.04,
                "50000": 0.037,
                "100000": 0.035
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "",
                "speed": "",
                "min": "",
                "max": "",
                "requirements": "",
                "details": ""
            },
            "card": null,
            "main": 0
        },
        {
            "id": 13,
            "title": "Автопросмотры - 5 руб",
            "tag": "INSTAGRAM_AUTO_VIEWS_LK",
            "show_group": "GROUP_AUTO",
            "pay_group": "GROUP_VIEWS",
            "price_list": {
                "1": 0.05,
                "1000": 0.045,
                "5000": 0.043,
                "10000": 0.04,
                "25000": 0.04,
                "50000": 0.037,
                "100000": 0.035
            },
            "img": "\/svg\/media-player.svg",
            "description": {
                "startup": "В течение 5-10 минут",
                "speed": "1000 в минуту",
                "min": "100",
                "max": "50000",
                "requirements": "Должен быть открытым и иметь аватарку",
                "details": "Всегда работают! Без каких либо зависаний Первое видео выкладывать не раньше, чем через 10 мин после добавления заказа, нужно время на обработку. Указывать только логин!!!"
            },
            "card": null,
            "main": 0
        }
    ]
}
```

### HTTP Request
`GET api/user_services`


<!-- END_264edcc5117b6d97de111ef5f546cb45 -->

<!-- START_6bf8db6b11a00ea78f299d230d4bbad4 -->
## User services for main
Список пользовательских сервисов, у которых visible=1, main=1

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/user_services/main" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/user_services/main"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "success",
    "message": "Сервисы на главной",
    "data": []
}
```

### HTTP Request
`GET api/user_services/main`


<!-- END_6bf8db6b11a00ea78f299d230d4bbad4 -->

<!-- START_a943ffff0164dc98ebdbec4b8c920896 -->
## User services by group
Список пользовательских сервисов в группе, у которых visible=1

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/user_services/show_group/necessitatibus" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/user_services/show_group/necessitatibus"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "success",
    "message": "Пользовательские сервисы",
    "data": []
}
```

### HTTP Request
`GET api/user_services/show_group/{show_group}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `show_group` |  optional  | string required группа

<!-- END_a943ffff0164dc98ebdbec4b8c920896 -->

<!-- START_c2a203097faa4ffc2893fd73ad730405 -->
## User service by tag
Поиск пользовательского сервиса по тегу

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/user_services/rerum" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/user_services/rerum"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Eloquent\\Builder.php",
        "line": 472,
        "message": "No query results for model [App\\UserService].",
        "class": "Illuminate\\Database\\Eloquent\\ModelNotFoundException"
    }
}
```

### HTTP Request
`GET api/user_services/{tag}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `tag` |  optional  | string required уникальный тег сервиса

<!-- END_c2a203097faa4ffc2893fd73ad730405 -->

<!-- START_3f822ef723a0d68bdb9e7f9b272e4169 -->
## Update UserService
Админ: обновить данные пользовательского сервиса

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/user_services/perspiciatis" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/user_services/perspiciatis"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST api/user_services/{tag}`

#### URL Parameters

Parameter | Status | Description
--------- | ------- | ------- | -------
    `tag` |  optional  | string required уникальный тег сервиса

<!-- END_3f822ef723a0d68bdb9e7f9b272e4169 -->

#users


<!-- START_1fdf5c126c9b5b722e5044c3f680bf8e -->
## Find user by name or email

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
Администратор: поиск пользователя по имени или email

> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/admin/users" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"name":"perferendis","email":"et"}'

```

```javascript
const url = new URL(
    "http://smmtouch.store/api/admin/users"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "perferendis",
    "email": "et"
}

fetch(url, {
    method: "GET",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php",
        "line": 82,
        "message": "Unauthenticated.",
        "class": "Illuminate\\Auth\\AuthenticationException"
    }
}
```

### HTTP Request
`GET api/admin/users`

#### Body Parameters
Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    `name` | string |  optional  | имя
        `email` | string |  optional  | email
    
<!-- END_1fdf5c126c9b5b722e5044c3f680bf8e -->

<!-- START_2b6e5a4b188cb183c7e59558cce36cb6 -->
## User details
Информация о текущем пользователе

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/user" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/user"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`GET api/user`


<!-- END_2b6e5a4b188cb183c7e59558cce36cb6 -->

<!-- START_f0654d3f2fc63c11f5723f233cc53c83 -->
## Update user data
Обновить данные пользователя, кроме пароля и аватара
Записывает все поля запроса в текущего пользователя

> Example request:

```bash
curl -X POST \
    "http://smmtouch.store/api/user" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/user"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
null
```

### HTTP Request
`POST api/user`


<!-- END_f0654d3f2fc63c11f5723f233cc53c83 -->

<!-- START_fc1e4f6a697e3c48257de845299b71d5 -->
## List of users
Модератор: список пользователей

<br><small style="padding: 1px 9px 2px;font-weight: bold;white-space: nowrap;color: #ffffff;-webkit-border-radius: 9px;-moz-border-radius: 9px;border-radius: 9px;background-color: #3a87ad;">Requires authentication</small>
> Example request:

```bash
curl -X GET \
    -G "http://smmtouch.store/api/users" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://smmtouch.store/api/users"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response (200):

```json
{
    "status": "error",
    "message": "Exception caught in handler",
    "data": {
        "file": "C:\\work\\smm-api\\vendor\\laravel\\framework\\src\\Illuminate\\Auth\\Middleware\\Authenticate.php",
        "line": 82,
        "message": "Unauthenticated.",
        "class": "Illuminate\\Auth\\AuthenticationException"
    }
}
```

### HTTP Request
`GET api/users`


<!-- END_fc1e4f6a697e3c48257de845299b71d5 -->


