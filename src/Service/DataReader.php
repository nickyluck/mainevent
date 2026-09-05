<?php

namespace App\Service;

class DataReader
{
    const STRUCTURE_FILE = "structure.csv";
    const DOTATION_FILE = "dotation.csv";
    const RANKING_FILE = "classement.csv";

    public static function getStructure($hours, $minutes)
    {
        $structure = [];

        $number = 1;
        $time = new Time($hours, $minutes);

        $file = fopen("tmp/" . DataReader::STRUCTURE_FILE, "r");

        $read = false;
        while (($line = fgetcsv($file)) !== false) {
            $line = explode(";", $line[0]);
            if ($read) {
                if (empty($line[0])) {
                    $level = [
                        'pause' => true,
                        'label' => $line[4],
                        'time' => $time->toString()
                    ];
                } else {
                    $level = [
                        'pause' => false,
                        'SB' => $line[0],
                        'BB' => $line[1],
                        'Ante' => $line[2],
                        'time' => $time->toString(),
                        'number' => $number
                    ];
                    $number++;
                }
                $time->addTime(0, $line[3], 0);

                $structure[] = $level;
            } else {
                $read = true;
            }
        }

        fclose($file);
        return $structure;
    }

    public static function getDotation()
    {
        $dotation = [];

        $file = fopen("tmp/" . DataReader::DOTATION_FILE, "r");

        $read = false;
        while (($line = fgetcsv($file)) !== false) {
            $line = explode(";", $line[0]);
            if ($read) {
                if ($line[0] == $line[1]) {
                    $price = [
                        'classement' => ($line[0] == '1') ? "1<sup>er</sup>" : "{$line[0]}<sup>e</sup>",
                        'prix' => $line[2] . " (valeur {$line[3]}€)"
                    ];
                } else {
                    $price = [
                        'classement' => "{$line[0]}<sup>e</sup> - {$line[1]}<sup>e</sup>",
                        'prix' => $line[2] . " (valeur {$line[3]}€)"
                    ];
                }

                $dotation[] = $price;
            } else {
                $read = true;
            }
        }

        fclose($file);
        return $dotation;
    }

    public static function getDotation150()
    {
        $dotation = [];

        $file = fopen("tmp/dotation150.csv", "r");

        $read = false;
        while (($line = fgetcsv($file)) !== false) {
            $line = explode(";", $line[0]);
            if ($read) {
                if ($line[0] == $line[1]) {
                    $price = [
                        'classement' => ($line[0] == '1') ? "1<sup>er</sup>" : "{$line[0]}<sup>e</sup>",
                        'prix' => $line[2] . " (valeur {$line[3]}€)"
                    ];
                } else {
                    $price = [
                        'classement' => "{$line[0]}<sup>e</sup> - {$line[1]}<sup>e</sup>",
                        'prix' => $line[2] . " (valeur {$line[3]}€)"
                    ];
                }

                $dotation[] = $price;
            } else {
                $read = true;
            }
        }

        fclose($file);
        return $dotation;
    }

    public static function getRanking()
    {
        $players = [];
        $prizepool = self::getPrizepool();

        $file = fopen("tmp/" . DataReader::RANKING_FILE, "r");

        $read = false;
        while (($line = fgetcsv($file)) !== false) {
            $line = explode(";", $line[0]);
            if ($read) {
                $player = [
                    'position' => $line[0],
                    'pseudo' => $line[1],
                    'club' => $line[2],
                    'prix' => (array_key_exists($line[0], $prizepool)) ? $prizepool[$line[0]] : ''
                ];
                $players[] = $player;
            } else {
                $read = true;
            }
        }

        fclose($file);

        return $players;
    }

    private static function getPrizepool()
    {
        $prizepool = [];

        $file = fopen("tmp/" . DataReader::DOTATION_FILE, "r");

        $read = false;
        while (($line = fgetcsv($file)) !== false) {
            $line = explode(";", $line[0]);
            if ($read) {
                for ($i = $line[0]; $i <= $line[1]; $i++) {
                    $prizepool[$i] = $line[2] . " (valeur {$line[3]}€)";
                }
            } else {
                $read = true;
            }
        }

        fclose($file);
        return $prizepool;
    }
}
