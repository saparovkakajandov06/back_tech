<?php


namespace App\PaymentSystems;


use App\Payment;
use Illuminate\Http\Request;

interface PaymentSystem
{
    public function hasCurrency(string $cur): bool;
    public function createRemotePayment(Payment $payment, array $paymentData): array;

    public function checkSignature(Request $request): void;
    public function mapRequestToStatus($request): string;
    public function getForeignPaymentId($request): string;
    public function getDefaultResponse(): mixed;
}