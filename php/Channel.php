<?php

class Channel
{
    public $folder = 'txt';
    public $fileName;
    public $startTime;
    public $endTime;
    public $afterDot = false;
    public $lowerCase = false;

    public function __construct($fileName)
    {

        $this->fileName = $fileName;

        switch ($this->fileName) {
            case 'history_s.txt':
            case 'disc_eu_s.txt':
            case 'disc_rus_s.txt':
            case 'tvci_s.txt':
            case 'animal_s.txt':
                $this->startTime = '10:00';
                $this->endTime = '01:00';
                $this->afterDot = true;
                break;
            case 'cultura_s.txt':
                $this->startTime = '10:00';
                $this->endTime = '00:00';
                $this->afterDot = true;
                break;
            case 'tv1000_s.txt':
            case 'rtvi_s.txt':
            case 'esp_s.txt':
                $this->startTime = '09:00';
                $this->endTime = '01:00';
                break;
            case 'match-planeta_s.txt':
                $this->startTime = '08:00';
                $this->endTime = '06:00';
                $this->afterDot = true;
                break;
            case 'match-tv_s.txt':
                $this->startTime = '09:30';
                $this->endTime = '01:00';
                break;
            case 'usadba_s.txt':
            case 'ohota_s.txt':
                $this->startTime = '12:00';
                $this->endTime = '00:00';
                $this->afterDot = true;
                break;
            case 'rtrpl_s.txt':
                $this->startTime = '06:00';
                $this->endTime = '03:00';
                break;
            case 'tv1000action_s.txt':
            case 'tv1000k_s.txt':
            case 'tv21_s.txt':
                $this->startTime = '08:00';
                $this->endTime = '02:00';
                break;
            case 'vremya_s.txt':
                $this->startTime = '08:00';
                $this->endTime = '03:00';
                $this->afterDot = true;
                break;
            case 'nostalg_s.txt':
                $this->startTime = '08:00';
                $this->endTime = '01:00';
                break;
            case 'geogr_s.txt':
                $this->startTime = '09:30';
                $this->endTime = '00:00';
                $this->afterDot = true;
                break;
            default;
                $this->startTime = '08:00';
                $this->endTime = '05:00';
                break;
        }

        $_POST['afterDot'] = $this->afterDot;
        $_POST['lowerCase'] = $this->lowerCase;
    }

    public function getLinesUTF8(): array
    {
        $arrayOfStr = file("$this->folder/$this->fileName");
        return array_map(fn ($line) => trim(iconv('CP1251', 'UTF-8', $line)), $arrayOfStr);
    }

    public function getLinesByFileName(): array
    {
        return getParsedArr($this->getLinesUTF8(), $this->startTime, $this->endTime);
    }
}
