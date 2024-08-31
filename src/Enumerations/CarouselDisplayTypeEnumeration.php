<?php

namespace App\Enumerations;

use Eloquent\Enumeration\AbstractEnumeration;

class CarouselDisplayTypeEnumeration extends AbstractEnumeration
{
    const TYPE_FLIP = "Flip";
    const TYPE_FADE = 'Fade';
    const TYPE_SLIDE  = 'Slide';
    const TYPE_CRAWL = 'Crawl';

    public static function getChoices(): array
    {
        return [
            self::TYPE_FLIP => self::TYPE_FLIP,
            self::TYPE_FADE => self::TYPE_FADE,
            self::TYPE_SLIDE => self::TYPE_SLIDE,
//            self::TYPE_CRAWL => self::TYPE_CRAWL,
        ];
    }

}