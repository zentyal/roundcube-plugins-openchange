<?php
require_once(dirname(__FILE__) . '/../../zentyal_lib/OpenchangeDebug.php');

class OCParsing
{
    public static $fullEventProperties = array(
        PidTagNormalizedSubject,
        //PidTagHasAttachments,
        PidLidBusyStatus, PidLidLocation,
        PidLidAppointmentStartWhole, PidLidAppointmentEndWhole,
        //PidNameKeywords,
        PidLidAppointmentSubType, PidTagLastModificationTime,
        //PidLidAllAttendeesString,
        PidTagBody, PidTagSensitivity, PidTagPriority,
        //PidLidAppointmentRecur,
    );

    //TODO: Qué hacer con PidLidCommoni{Start/End} & PidTag{Start/End}Date
    public static $eventTranslationTable = array(
        //PidLidResponseStatus,PidLidRecurring
        PidTagNormalizedSubject     => array('field' => 'title'),
//        PidTagHasAttachments        => array('field' => False),
        PidLidBusyStatus            => array('field' => 'free_busy', 'parsingFunc' => 'parseBusy'),
        PidLidLocation              => array('field' => 'location'),
        PidLidAppointmentStartWhole => array('field' => 'start', 'parsingFunc' => 'parseDate'),
        PidLidAppointmentEndWhole   => array('field' => 'end', 'parsingFunc' => 'parseDate'),
//        PidNameKeywords             => array('field' => 'categories'), //ptypmultiplestring
        PidLidAppointmentSubType    => array('field' => 'allday', 'parsingFunc' => 'parseBool'),
        PidTagLastModificationTime  => array('field' => 'changed', 'parsingFunc' => 'parseDate'),
//        PidLidAllAttendeesString    => array('field' => 'attendees'), //this will need some parsing
//        PidLidAppointmentRecur      => array('field' => 'recurrence'), //binary, parsing needed, PidLidRecurrenceType, PidLidRecurrencePattern
        PidTagBody                  => array('field' => 'description', 'parsingFunc' => 'parseDescription'),
        PidTagSensitivity           => array('field' => 'sensivity'),
        PidTagPriority              => array('field' => 'priority'),
    );

    private static $busyTranslation = array(
        0 => 'Free',
        1 => 'Tentative',
        2 => 'Busy',
        3 => 'Out of Office',
    );

    /**
     * Returns a simple message ID from a composed one
     *
     * @param string $complexId The id with the form "folderID/messageID"
     * @param bool $convertToHexString If we want to return a "0xhexnumber" string
     *
     * @return string The hex string of the messageID
     */
    public static function complex_id_to_single($complexId, $convertToHexString=true)
    {
        $ids = explode("/", $complexId);

        if ($convertToHexString)
            $ids[1] = "0x" . $ids[1];

        return $ids[1];
    }

    public static function createWithProperties($calendar, $properties)
    {
        return call_user_func_array(array($calendar, 'createMessage'), $properties);
    }

    public static function setProperties($fetchedEvent, $properties)
    {
        $setResult = call_user_func_array(array($fetchedEvent, 'set'), $properties);

        return $setResult;
    }

    public static function deleteEvents($calendar, $eventsIds)
    {
        if (!is_array($eventsIds))
            $eventsIds = array($eventsIds);

        return call_user_func_array(array($calendar, 'deleteMessages'), $eventsIds);
    }

    public static function parseRc2OcEvent($event)
    {
        $props = array();

        foreach ($event as $field => $value) {
            $ocProp = self::parseKeyRc2Oc($field);
            if ($ocProp) {
                $ocValue = self::parseValueRc2Oc($ocProp, $value);

                if (is_bool($ocValue) || $ocValue){
                    $props = array_merge($props, array($ocProp, $ocValue));
                }
            }
        }

        return $props;
    }

    /*
     * All-day event adjustment before saving "edit changes"
     */
    public static function checkAllDayConsistency($event)
    {
        if ($event['allday']){
//            $hourEnd = intval($event['end']->format('H'));
//            $hourStart = intval($event['start']->format('H'));
//            $hourDiff = $hourEnd - $hourStart;

//            $event['end']->sub(date_interval_create_from_date_string($hourDiff . ' hour'));
            $event['start']->setTime(12, 0);
            $event['end']->setTime(12, 0);
        }

        return $event;
    }

    private static function parseKeyRc2Oc($rcField)
    {
        foreach (self::$eventTranslationTable as $ocProp => $rcProps) {
            if ($rcField == $rcProps['field'])
                return $ocProp;
        }

        return False;
    }

    private static function parseValueRc2Oc($ocProp, $value)
    {
        if (array_key_exists($ocProp, self::$eventTranslationTable)) {
            $rcubeEventProps = self::$eventTranslationTable[$ocProp];
            if (array_key_exists('parsingFunc', $rcubeEventProps)) {
                $func = $rcubeEventProps['parsingFunc'] . "Rc2Oc";
                $value = call_user_func_array(array(self, $func), array($value));
            }

            return $value;
        }

        return False;

    }

    public static function parseEventOc2Rc($event)
    {
        $result = array();

        foreach ($event as $prop => $value) {
            $key = self::parseOc2RcKey($prop);
            $value = self::parseOc2RcValue($prop, $value);

            $result[$key] = $value;
        }

//        $result['end'] = self::correctAllDayEndDateOc2Rc($result);

        return $result;
    }

    public static function getFullEventProps($calendar, $ocMessage)
    {
        $result = array();

        $result = call_user_func_array(array($ocMessage, 'get'), self::$fullEventProperties);

        $id = $ocMessage->getID();
        $result['event_id'] = $id;
        $result['uid'] = $id;
        $result['id'] = $id;
        $result['calendar'] = $calendar->getID();

        return $result;
    }

    private static function parseOc2RcKey($ocProp)
    {
        if (array_key_exists($ocProp, self::$eventTranslationTable)) {
            $rcubeEventProps = self::$eventTranslationTable[$ocProp];
            return $rcubeEventProps['field'];
        }

        return $ocProp;
    }

    private static function parseOc2RcValue($ocProp, $value)
    {
        if (array_key_exists($ocProp, self::$eventTranslationTable)) {
            $rcubeEventProps = self::$eventTranslationTable[$ocProp];
            if (array_key_exists('parsingFunc', $rcubeEventProps)) {
                $func = $rcubeEventProps['parsingFunc'] . "Oc2Rc";
                $value = call_user_func_array(array(self, $func), array($value));
            }
        }

        return $value;
    }

    /**
     * Converts a given date obtained from OChange stores to a DateTime object
     * used by RCube
     *
     * @param string $date The date we want to convert
     *
     * @return DateTime The converted date
     */
    private static function parseDateOc2Rc($unixTimestampDate)
    {
        $parsedDate = new DateTime();
        $parsedDate->setTimestamp($unixTimestampDate);

        return $parsedDate;
    }

    private static function parseDateRc2Oc($date)
    {
        return $date->getTimestamp();
    }

    private static function parseBusyOc2Rc($state)
    {
        return self::$busyTranslation[$state];
    }

    private static function parseBusyRc2Oc($state)
    {
        foreach (self::$busyTranslation as $ocState => $rcState) {
            if ($rcState == $state)
                return $ocState;
        }

        return 0;
    }

    private static function parseDescriptionOc2Rc($description)
    {
        $description = ltrim($description, ')');

        if (strpos($description, "\r\n\n") !== false) {
            $exploded = explode("\r\n\n", $description, -1);
            $description = join($exploded, "\n");
        }

        return $description;
    }

    private static function parseDescriptionRc2Oc($description)
    {
        return $description;
    }

    private static function correctAllDayEndDateOc2Rc($event)
    {
        if ($event['allday']) {
            return $event['end']->sub(date_interval_create_from_date_string('1 day'));
        }

        return $event['end'];
    }

    private static function parseBoolOc2Rc($string)
    {
        if ($string == "0")
            return false;
        else
            return true;
    }

    private static function parseBoolRc2Oc($bool)
    {
        if ($bool)
            return true;
        else
            return false;
    }
}
?>
