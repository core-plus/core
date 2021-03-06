<?php

namespace Core\Support;

class ManageRepository
{
    protected static $manages = [];

    /**
     * Push manage url.
     *
     * @param string $name
     * @param string $uri
     * @param array $option
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    public function loadManageFrom(string $name, string $uri, array $option = [])
    {
        static::$manages[] = [
            'name' => $name,
            'uri' => $uri,
            'option' => $option,
        ];
    }

    /**
     * Get the manages for the provider.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function getManages(): array
    {
        $manages = [];
        foreach (static::$manages as $item) {
            $name = $item['name'];
            $uri = $item['uri'];
            $option = $item['option'];

            $isRoute = $option['route'] ?? false;
            $parameters = (array) ($option['parameters'] ?? []);
            $absolute = $option['absolute'] ?? true;
            $icon = $option['icon'] ?? null;

            $manages[] = [
                'name' => $name,
                'icon' => $icon,
                'uri' => ! $isRoute ? $uri : route($uri, $parameters, $absolute),
            ];
        }

        return $manages;
    }
}
