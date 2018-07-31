<?php
class DateTimeUtil {

    /**
     * @param string $dateTime
     * @return string
     */
    static function convertDateTimeToUtcTimezoneTimestamp(string $dateTime): string {
        $date = new DateTime("@$dateTime", new DateTimeZone(getUserTimezone()));
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->getTimestamp();
    }

    /**
     * @param string $timestamp
     * @return string
     */
    static function convertTimestampToMorgueTimezoneDateTime(string $timestamp): string {
        $date = new DateTime("@$timestamp", new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone(getUserTimezone()));
        return $date->format('Y-m-d h:iA');
    }

}