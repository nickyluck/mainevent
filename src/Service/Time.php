<?php

namespace App\Service;

class Time
{
    private $hours;
    private $minutes;
    private $seconds;

    public function __construct($hours, $minutes = 0, $seconds = 0)
    {
        $this->hours = $hours;
        $this->minutes = $minutes;
        $this->seconds = $seconds;
    }

    public function toSeconds()
    {
        return $this->hours * 3600 + $this->minutes * 60 + $this->seconds;
    }

    public function toTime($seconds)
    {
        $this->hours = intdiv($seconds, 3600) % 24;
        $seconds = $seconds % 3600;
        $this->minutes = intdiv($seconds, 60);
        $this->seconds = $seconds % 60;
    }

    public function addTime($hours, $minutes, $seconds)
    {
        $time = new Time($hours, $minutes, $seconds);
        $seconds = $this->toSeconds() + $time->toSeconds();
        $this->toTime($seconds);
    }

    public function toString()
    {
        $hours = (0 <= $this->hours && $this->hours <= 9) ? '0' . $this->hours : $this->hours;
        $minutes = (0 <= $this->minutes && $this->minutes <= 9) ? '0' . $this->minutes : $this->minutes;
        return $hours . "H" . $minutes;
    }
}
