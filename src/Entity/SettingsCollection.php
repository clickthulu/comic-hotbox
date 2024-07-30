<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class SettingsCollection
{
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function addItem(Settings $item): SettingsCollection
    {
        $this->items->set($item->getSetting(), $item);
        return $this;
    }

    public function getItems(): ArrayCollection
    {
        return $this->items;
    }

    public function get(string $setting): Settings
    {
        return $this->items->get($setting);
    }

}