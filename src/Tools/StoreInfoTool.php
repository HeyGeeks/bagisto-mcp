<?php

namespace HeyGeeks\BagistoMCP\Tools;

class StoreInfoTool extends BaseTool
{
    public function name(): string
    {
        return 'store.info';
    }

    public function description(): string
    {
        return 'Get general store information including name, currency, locale, and contact details. Use this tool when the user asks about the store, wants contact information, or needs to know about supported currencies and languages.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
            'required' => [],
            'description' => 'No input required.',
        ];
    }

    public function execute(array $arguments): array
    {
        $channel = core()->getCurrentChannel();

        return [
            'store' => [
                'name' => $channel->name ?? config('app.name'),
                'code' => $channel->code ?? 'default',
                'description' => strip_tags($channel->description ?? ''),
                'hostname' => $channel->hostname ?? request()->getHost(),
            ],
            'locale' => [
                'current' => core()->getCurrentLocale()->code ?? 'en',
                'available' => $channel->locales->pluck('code')->toArray(),
            ],
            'currency' => [
                'current' => core()->getCurrentCurrencyCode(),
                'base' => core()->getBaseCurrencyCode(),
                'available' => $channel->currencies->pluck('code')->toArray(),
            ],
            'contact' => [
                'email' => core()->getConfigData('general.general.email.store_email_address') ?? null,
                'phone' => core()->getConfigData('general.general.contact.phone_number') ?? null,
            ],
            'social' => [
                'facebook' => core()->getConfigData('general.social.links.facebook') ?? null,
                'twitter' => core()->getConfigData('general.social.links.twitter') ?? null,
                'instagram' => core()->getConfigData('general.social.links.instagram') ?? null,
            ],
        ];
    }
}
