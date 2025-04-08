<?php

/**
 * @license MIT
 */

namespace SinyorPos\DataMapper\RequestDataMapper;

use SinyorPos\Entity\Account\AbstractPosAccount;
use SinyorPos\Entity\Card\CreditCardInterface;
use SinyorPos\Event\Before3DFormHashCalculatedEvent;
use SinyorPos\Gateways\EstV3Pos;

/**
 * Creates request data for EstPos Gateway requests that supports v3 Hash algorithm
 */
class EstV3PosRequestDataMapper extends EstPosRequestDataMapper
{
    /**
     * {@inheritDoc}
     */
    public function create3DFormData(AbstractPosAccount $posAccount, array $order, string $paymentModel, string $txType, string $gatewayURL, ?CreditCardInterface $creditCard = null): array
    {
        $order = $this->preparePaymentOrder($order);

        $data = $this->create3DFormDataCommon($posAccount, $order, $paymentModel, $txType, $gatewayURL, $creditCard);

        $data['inputs']['TranType'] = $this->mapTxType($txType);
        unset($data['inputs']['islemtipi']);

        $data['inputs']['hashAlgorithm'] = 'ver3';

        $event = new Before3DFormHashCalculatedEvent(
            $data['inputs'],
            $posAccount->getBank(),
            $txType,
            $paymentModel,
            EstV3Pos::class
        );
        $this->eventDispatcher->dispatch($event);
        $data['inputs'] = $event->getFormInputs();

        $data['inputs']['hash'] = $this->crypt->create3DHash($posAccount, $data['inputs']);

        return $data;
    }
}
