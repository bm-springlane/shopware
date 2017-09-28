<?php declare(strict_types=1);

namespace Shopware\TaxAreaRule\Extension;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Factory\ExtensionInterface;
use Shopware\Search\QueryBuilder;
use Shopware\Search\QuerySelection;
use Shopware\TaxAreaRule\Event\TaxAreaRuleBasicLoadedEvent;
use Shopware\TaxAreaRule\Event\TaxAreaRuleWrittenEvent;
use Shopware\TaxAreaRule\Struct\TaxAreaRuleBasicStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class TaxAreaRuleExtension implements ExtensionInterface, EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            TaxAreaRuleBasicLoadedEvent::NAME => 'taxAreaRuleBasicLoaded',
            TaxAreaRuleWrittenEvent::NAME => 'taxAreaRuleWritten',
        ];
    }

    public function joinDependencies(
        QuerySelection $selection,
        QueryBuilder $query,
        TranslationContext $context
    ): void {
    }

    public function getDetailFields(): array
    {
        return [];
    }

    public function getBasicFields(): array
    {
        return [];
    }

    public function hydrate(
        TaxAreaRuleBasicStruct $taxAreaRule,
        array $data,
        QuerySelection $selection,
        TranslationContext $translation
    ): void {
    }

    public function taxAreaRuleBasicLoaded(TaxAreaRuleBasicLoadedEvent $event): void
    {
    }

    public function taxAreaRuleWritten(TaxAreaRuleWrittenEvent $event): void
    {
    }
}