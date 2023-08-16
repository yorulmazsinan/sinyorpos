<?php

namespace EceoPos;

use EceoPos\Entity\Card\AbstractCreditCard;
use EceoPos\Exceptions\UnsupportedPaymentModelException;
use EceoPos\Gateways\AbstractGateway;
use Symfony\Component\HttpFoundation\Request;

/**
 * PosInterface Arayüzü
 */
interface PosInterface
{
    /**
     * XML olarak DOM belgesini oluşturur:
     *
     * @param  array  $nodes
     * @param  string  $encoding
     * @param  bool  $ignorePiNode ; doğru olduğunda, onu bu düğümle sarmaz: <?xml version="1.0" encoding="UTF-8"?>
     * @return string; XML, veya bir hata oluştuysa false.
     */
    public function createXML(array $nodes, string $encoding = 'UTF-8', bool $ignorePiNode = false): string;

    /**
     * @return null|string
     *
     * @deprecated sadece emptyStringsToNull() işlevini çağırın
     */
    public function printData($data): ?string;

    /**
     * Regular Payment
     *
     * @return AbstractGateway
     */
    public function makeRegularPayment();

    /**
     * 3D Ödeme Yap
     *
     * @param  Request  $request
     * @return AbstractGateway
     */
    public function make3DPayment(Request $request);

    /**
     * 3D Pay Ödemesi Yap
     *
     * @param  Request  $request
     * @return AbstractGateway
     */
    public function make3DPayPayment(Request $request);

    /**
     * Yalnızca ana bilgisayar, ödeme yanıtının biçimlendirilmiş verilerini döndürür.
     *
     * @param  Request  $request
     * @return AbstractGateway
     */
    public function make3DHostPayment(Request $request);

    /**
     * İçeriği "WebService"e gönderir.
     *
     * @param  array|string  $contents
     * @param  string|null  $url
     * @return mixed
     */
    public function send($contents, ?string $url = null);

    /**
     * Prepare Order
     *
     * @param  array  $order
     * @param  string  $txType ; AbstractGateway için geçerli bir işlem türü.
     * @param  AbstractCreditCard|null  $card ; "3DFormData" talep edildiğinde kullanılır.
     * @return self
     */
    public function prepare(array $order, string $txType, $card = null);

    /**
     * Ödeme Yap
     *
     * @param  AbstractCreditCard  $card
     * @return AbstractGateway
     *
     * @throws UnsupportedPaymentModelException
     */
    public function payment($card);

    /**
     * Siparişi İade Et
     *
     * @return AbstractGateway
     */
    public function refund();

    /**
     * Siparişi İptal Et
     *
     * @return AbstractGateway
     */
    public function cancel();

    /**
     * Sipariş Durumu
     *
     * @return AbstractGateway
     */
    public function status();

    /**
     * Sipariş Geçmişi
     *
     * @param  array  $meta
     * @return AbstractGateway
     */
    public function history(array $meta);

    /**
     * Başarılı mı?
     *
     * @return bool
     */
    public function isSuccess();

    /**
     * @return bool
     *
     * @deprecated için isSuccess() işlevini kullanın.
     */
    public function isError();

    /**
     * Test modunu etkinleştirir veya devre dışı bırakır.
     *
     * @param  bool  $testMode
     * @return AbstractGateway
     */
    public function setTestMode(bool $testMode);

    /**
     * @return bool
     */
    public function testMode();
}
