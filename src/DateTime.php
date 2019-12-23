<?php
namespace nuffy\SDV;

class DateTime{
    private $timestamp;

    const DAYS_PER_SEASON = 28;
    const SEASONS_PER_YEAR = 4;
    const HOURS_PER_DAY = 24;
    const MINUTES_PER_HOUR = 60;

    const SEASONS = ["Spring", "Summer", "Autumn", "Winter"];

    protected $translator;

    public function __construct(string $date = "001-1-01 06:00")
    {
        $this->timestamp = $this->parseString($date);

        if($this->timestamp < 0){
            throw new Exceptions\DateTimeException("Dates before 01-1-01 is currently not supported.");
        }
    }

    protected function parseString(string $datetime_string) : ?int
    {
        $matches = [];
        $date_pattern = '([\d]{0,3})-([1-4])-([0-1]?[0-9]|2[0-8])';
        $time_pattern = '([0-2]?[0-9]):([0-5][0-9])';
        if(is_numeric($datetime_string)){
            // Assume this is a timestamp (in minutes), and just return it
            return $datetime_string;
        }
        if(\preg_match('/^'.$date_pattern.' '.$time_pattern.'$/', $datetime_string, $matches)){
            // This is a datetime stamp like thing (y-m-d h:m)
            list($match, $year, $season, $day, $hours, $minutes) = $matches;
            $year = (int)$year ?? 1;
            $hours = (int)$hours == 0 && (int)$minutes == 0 ? "24" : $hours;
            $return = $minutes;
            $return += $hours * self::MINUTES_PER_HOUR;
            $return += ($day - 1) * self::HOURS_PER_DAY * self::MINUTES_PER_HOUR;
            $return += ($season - 1) * self::HOURS_PER_DAY * self::MINUTES_PER_HOUR * self::DAYS_PER_SEASON;
            $return += ($year - 1) * self::HOURS_PER_DAY * self::MINUTES_PER_HOUR * self::DAYS_PER_SEASON * self::SEASONS_PER_YEAR;
            return $return;
        }
        if(\preg_match('/^'.$date_pattern.'$/', $datetime_string, $matches)){
            // This is a date stamp like thing (y-m-d)
            return $this->parseString($datetime_string." 06:00");
        }
        if(\preg_match('/^('.implode("|", self::SEASONS).') ([0-1]?[0-9]|2[0-8])$/', $datetime_string, $matches)){
            // This looks like the wiki def (eg. "Summer 12")
            $season = $matches[1];
            switch($season){
                case "Spring":
                    $season = 1;
                    break;
                case "Summer":
                    $season = 2;
                    break;
                case "Winter":
                    $season = 3;
                    break;
                case "Autumn":
                    $season = 4;
                    break;
            }
            return $this->parseString("1-$season-".(int)$matches[2]);
        }
        throw new Exceptions\DateTimeException("Unrecognized date format.");
    }

    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    public function getMinute() : int
    {
        return $this->timestamp % self::MINUTES_PER_HOUR;
    }

    public function getHour() : int
    {
        return floor($this->timestamp / self::MINUTES_PER_HOUR) % self::HOURS_PER_DAY ;
    }

    public function getDay() : int
    {
        return ceil($this->timestamp / (self::MINUTES_PER_HOUR * self::HOURS_PER_DAY));
    }

    public function getDayOfSeason() : int
    {
        return ($this->getDay() - 1) % self::DAYS_PER_SEASON + 1;
    }

    public function getSeason() : int
    {
        return (($this->getDay() - 1) / self::DAYS_PER_SEASON) % self::SEASONS_PER_YEAR + 1;
    }

    public function getYear() : int
    {
        return ceil(($this->getDay() - 1) / self::DAYS_PER_SEASON / self::SEASONS_PER_YEAR);
    }

    public function __tooString() : string
    {
        return $string = $this->format("y-m-d H:i");
    }

    public function format(string $format)
    {
        $replacements = [
            "Y" => $this->getYear(),
            "y" => $this->getYear(),
            "m" => $this->getSeason(),
            "d" => $this->padZeros($this->getDayOfSeason(), 2),
            "j" => $this->getDay(),
            "H" => $this->padZeros($this->getHour(), 2),
            "i" => $this->padZeros($this->getMinute(), 2)
        ];
        $format = str_split($format);
        $escaped = false;
        foreach($format as &$letter){
            if($escaped){
                $escaped = false;
                continue;
            }elseif($letter == '\\'){
                $escaped = true;
                $letter = "";
                continue;
            }
            foreach($replacements as $needle=>$replacement){
                if($letter == $needle){
                    $letter = $replacement;
                }
            }
        }
        return implode("",$format);
    }

    public function diffDays(DateTime $date) : int
    {
        return $date->getDay() - $this->getDay();
    }

    public function diffForHumans(DateTime $date) : string
    {
        $diff = $this->diffDays($date);
        if($diff == 0){
            return "Today";
        }elseif($diff > 0){
            return "In ".$diff." days";
        }elseif($diff < 0){
            return $diff * -1 . " days ago";
        }
    }
    protected function padZeros(string $string, int $count) : string
    {
        return str_pad($string, $count, "0", \STR_PAD_LEFT);
    }
}