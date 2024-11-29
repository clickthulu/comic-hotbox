<?php

namespace App\Enumerations;

use Eloquent\Enumeration\AbstractEnumeration;
use Monolog\DateTimeImmutable;

class RotationFrequencyEnumeration extends AbstractEnumeration
{
    const LENGTH_MINUTE = 'Minute'; // Testing only
    const LENGTH_QUARTER = '15 Minutes'; // Testing only
    const LENGTH_HOUR = 'Hour';
    const LENGTH_DAY = 'Day';
    const LENGTH_WEEK = 'Week';
    const LENGTH_MONTH = 'Month';


    public static function getStarting(string $length): \DateTime
    {
        $current = new \DateTime();
        switch ($length) {
            case self::LENGTH_MINUTE:
                $current->setTime($current->format('H'), $current->format('i'), 0);
                break;
            case self::LENGTH_QUARTER:
                $minNow = $current->format("i");
                $quarter = 15 * (floor((int)$minNow/15));
                $current->setTime($current->format('H'), $quarter, 0);
                break;
            case self::LENGTH_HOUR:
                $current->setTime($current->format('H'), 0, 0);
                break;
            case self::LENGTH_WEEK:
                $week = strtotime("Last Sunday");

                $current->setTimestamp($week);
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

    public static function getNextStart(string $length, \DateTimeInterface $start): \DateTimeInterface
    {
        $lastDate = clone $start;
        switch ($length) {
            case self::LENGTH_MINUTE:
                $lastDate->modify("+1 minute");
                break;
            case self::LENGTH_QUARTER:
                $lastDate->modify("+15 minute");
                break;
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

    public static function getChoices()
    {
        return [
            self::LENGTH_MINUTE => self::LENGTH_MINUTE,
            self::LENGTH_QUARTER => self::LENGTH_QUARTER,
            self::LENGTH_HOUR => self::LENGTH_HOUR,
            self::LENGTH_DAY => self::LENGTH_DAY,
            self::LENGTH_WEEK => self::LENGTH_WEEK,
            self::LENGTH_MONTH => self::LENGTH_MONTH
        ];
    }
}