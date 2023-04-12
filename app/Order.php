<?php

namespace App;

/**
 * Order statuses
 */
class Order extends BaseModel {

// заказ обрабатывается перед запуском
//    const STATUS_PROCESSING = 'STATUS_PROCESSING';

    // создан, но не в работе
    const STATUS_CREATED = 'STATUS_CREATED';
    // разделен на чанки
    const STATUS_SPLIT = 'STATUS_SPLIT';
    // оплачен
    const STATUS_PAID = 'STATUS_PAID';
    // заказ в работе
    const STATUS_RUNNING = 'STATUS_RUNNING';
    // частично завершено
    const STATUS_PARTIAL_COMPLETED = 'STATUS_PARTIAL_COMPLETED';
    // заказ выполнен
    const STATUS_COMPLETED = 'STATUS_COMPLETED';
    // ошибка
    const STATUS_ERROR = 'STATUS_ERROR';
    // заказ отменен
    const STATUS_CANCELED = 'STATUS_CANCELED';
    // неизвестное состояние
    const STATUS_UNKNOWN = 'STATUS_UNKNOWN';
    // пауза
    const STATUS_PAUSED = 'STATUS_PAUSED';
    // обновляется
    const STATUS_UPDATING = 'STATUS_UPDATING';

    // заказ не найден
//    const STATUS_NOT_FOUND = 'STATUS_NOT_FOUND';
// заказ удален
//    const STATUS_DELETED = 'STATUS_DELETED';

//    const STATUS_BLOCKED = 'STATUS_BLOCKED';

//    const AVAILABLE_STATUSES = [
//        self::STATUS_CREATED,
//        self::STATUS_SPLIT,
//        self::STATUS_PAID,
//        self::STATUS_RUNNING,
//        self::STATUS_PARTIAL_COMPLETED,
//        self::STATUS_COMPLETED,
//        self::STATUS_ERROR,
//        self::STATUS_CANCELED,
//    ];
}
