<?php declare(strict_types=1);

namespace Shopware\OrderDelivery\Writer\Resource;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Write\Field\DateField;
use Shopware\Framework\Write\Field\FkField;
use Shopware\Framework\Write\Field\LongTextField;
use Shopware\Framework\Write\Field\ReferenceField;
use Shopware\Framework\Write\Field\StringField;
use Shopware\Framework\Write\Field\SubresourceField;
use Shopware\Framework\Write\Field\UuidField;
use Shopware\Framework\Write\Flag\Required;
use Shopware\Framework\Write\Resource;

class OrderDeliveryResource extends Resource
{
    protected const UUID_FIELD = 'uuid';
    protected const ORDER_STATE_UUID_FIELD = 'orderStateUuid';
    protected const TRACKING_CODE_FIELD = 'trackingCode';
    protected const SHIPPING_DATE_EARLIEST_FIELD = 'shippingDateEarliest';
    protected const SHIPPING_DATE_LATEST_FIELD = 'shippingDateLatest';
    protected const PAYLOAD_FIELD = 'payload';

    public function __construct()
    {
        parent::__construct('order_delivery');

        $this->primaryKeyFields[self::UUID_FIELD] = (new UuidField('uuid'))->setFlags(new Required());
        $this->fields[self::ORDER_STATE_UUID_FIELD] = (new StringField('order_state_uuid'))->setFlags(new Required());
        $this->fields[self::TRACKING_CODE_FIELD] = new StringField('tracking_code');
        $this->fields[self::SHIPPING_DATE_EARLIEST_FIELD] = (new DateField('shipping_date_earliest'))->setFlags(new Required());
        $this->fields[self::SHIPPING_DATE_LATEST_FIELD] = (new DateField('shipping_date_latest'))->setFlags(new Required());
        $this->fields[self::PAYLOAD_FIELD] = (new LongTextField('payload'))->setFlags(new Required());
        $this->fields['order'] = new ReferenceField('orderUuid', 'uuid', \Shopware\Order\Writer\Resource\OrderResource::class);
        $this->fields['orderUuid'] = (new FkField('order_uuid', \Shopware\Order\Writer\Resource\OrderResource::class, 'uuid'))->setFlags(new Required());
        $this->fields['shippingAddress'] = new ReferenceField('shippingAddressUuid', 'uuid', \Shopware\OrderAddress\Writer\Resource\OrderAddressResource::class);
        $this->fields['shippingAddressUuid'] = (new FkField('shipping_address_uuid', \Shopware\OrderAddress\Writer\Resource\OrderAddressResource::class, 'uuid'))->setFlags(new Required());
        $this->fields['shippingMethod'] = new ReferenceField('shippingMethodUuid', 'uuid', \Shopware\ShippingMethod\Writer\Resource\ShippingMethodResource::class);
        $this->fields['shippingMethodUuid'] = (new FkField('shipping_method_uuid', \Shopware\ShippingMethod\Writer\Resource\ShippingMethodResource::class, 'uuid'))->setFlags(new Required());
        $this->fields['positions'] = new SubresourceField(\Shopware\OrderDeliveryPosition\Writer\Resource\OrderDeliveryPositionResource::class);
    }

    public function getWriteOrder(): array
    {
        return [
            \Shopware\Order\Writer\Resource\OrderResource::class,
            \Shopware\OrderAddress\Writer\Resource\OrderAddressResource::class,
            \Shopware\ShippingMethod\Writer\Resource\ShippingMethodResource::class,
            \Shopware\OrderDelivery\Writer\Resource\OrderDeliveryResource::class,
            \Shopware\OrderDeliveryPosition\Writer\Resource\OrderDeliveryPositionResource::class,
        ];
    }

    public static function createWrittenEvent(array $updates, TranslationContext $context, array $errors = []): ?\Shopware\OrderDelivery\Event\OrderDeliveryWrittenEvent
    {
        if (empty($updates) || !array_key_exists(self::class, $updates)) {
            return null;
        }

        $event = new \Shopware\OrderDelivery\Event\OrderDeliveryWrittenEvent($updates[self::class] ?? [], $context, $errors);

        unset($updates[self::class]);

        $event->addEvent(\Shopware\Order\Writer\Resource\OrderResource::createWrittenEvent($updates, $context));
        $event->addEvent(\Shopware\OrderAddress\Writer\Resource\OrderAddressResource::createWrittenEvent($updates, $context));
        $event->addEvent(\Shopware\ShippingMethod\Writer\Resource\ShippingMethodResource::createWrittenEvent($updates, $context));
        $event->addEvent(\Shopware\OrderDelivery\Writer\Resource\OrderDeliveryResource::createWrittenEvent($updates, $context));
        $event->addEvent(\Shopware\OrderDeliveryPosition\Writer\Resource\OrderDeliveryPositionResource::createWrittenEvent($updates, $context));

        return $event;
    }
}