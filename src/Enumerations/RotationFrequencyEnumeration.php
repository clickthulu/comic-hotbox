<?php

namespace App\Enumerations;

use Eloquent\Enumeration\AbstractEnumeration;
use Monolog\DateTimeImmutable;

class RotationFrequencyEnumeration extends AbstractEnumeration
{
    const LENGTH_HOUR = 'Hour';
    const LENGTH_DAY = 'Day';
    const LENGTH_WEEK = 'Week';
    const LENGTH_MONTH = 'Month';


    public static function getStarting(string $length): \DateTime
    {
        $current = new \DateTime();
        switch ($length) {
            case self::LENGTH_HOUR:
                $current->setTime($current->format('H'), 0, 0);
                break;
            case self::LENGTH_WEEK:
                $week = new DateTimeImmutable('last sunday');
                $current->modify($week);
                $current->setTime(0,0,0);
                break;
            case self::LENGTH_MONTH:
                $current->setDate($current->format('Y'), $current->format('m'), 1);
                $current->setTime(0,0,0);
                break;
            case self::LENGTH_DAY:
            default:
                $current->setTime(0,0,0);
                break;
        }
        return $current;
    }

    public static function getNextStart(string $length, \DateTimeInterface $lastDate): \DateTime
    {
        switch ($length) {
            case self::LENGTH_HOUR:
                $lastDate->modify("+1 hour");
                break;
            case self::LENGTH_WEEK:
                $lastDate->modify("+1 week");
                $lastDate->setTime(0, 0, 0);
                break;
            case self::LENGTH_MONTH:
                $lastDate->modify("+1 month");
                $lastDate->setTime(0, 0, 0);
                break;
            case self::LENGTH_DAY:
            default:
                $lastDate->modify("+1 day");
                $lastDate->setTime(0, 0, 0);
                break;
        }
        return $lastDate;
    }
}