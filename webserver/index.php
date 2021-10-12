<?php
$awal = microtime(true);
// include'koneksi.php';
// include composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// create stemmer
// cukup dijalankan sekali saja, biasanya didaftarkan di service container
$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
$stemmer  = $stemmerFactory->createStemmer();

// stem
$sentence = $_GET['sentence'];
//$sentence = 'Pagar ditutupkan';
$text   = $stemmer->stem($sentence);
echo "Text Asli: " .$sentence;
echo "<br>";
echo "Text Stemming: " .$text;
echo "<br>";

function PreBmBc($pattern, $m, $bmBc = [])
{
    $i = 0;
    // echo "bmBc : ";
    for ($i = 0; $i < 256; $i++) {
        $bmBc[$i] = $m;
    }

    for ($i = 0; $i < $m - 1; $i++) {
        $bmBc[$pattern[$i]] = $m - 1 - $i;
        // echo "|" . $bmBc[$pattern[$i]] . "|";
    }
    echo "<br>";
    return $bmBc;
}

function PreBmGs($pattern, $m, $bmGs = [])
{
    $suff = suffix($pattern, $m);
    for ($i = 0; $i < $m; $i++) {
        $bmGs[$i] = $m;
    }

    // Case2
    // echo "bmGs Case 2 : ";
    $j = 0;
    for ($i = $m - 1; $i >= 0; $i--) {
        if ($suff[$i] == $i + 1) {
            for ($j; $j < $m - 1 - $i; $j++) {
                if ($bmGs[$j] == $m)
                    $bmGs[$j] = $m - 1 - $i;
                    // echo $bmGs[$j];
            }
        }
    }

    // Case1
    // echo "bmGs Case 1 : ";
    for ($i = 0; $i <= $m - 2; $i++) {
        $bmGs[$m - 1 - $suff[$i]] = $m - 1 - $i;
        // echo "|" . $bmGs[$m - 1 - $suff[$i]] . "|";
    }
    echo "<br>";
    return $bmGs;
}

function suffix($pattern, $m, $suff = [])
{
    $suff[$m - 1] = $m;
    // echo "suff : ";
    for ($i = $m - 2; $i >= 0; $i--) {
        $j = $i;
        while ($j >= 0 && $pattern[$j] == $pattern[$m - 1 - $i + $j]) {
            $j--;
        }

        $suff[$i] = $i - $j;
        // echo $pattern[$j];

    }
    echo "<br>";
    return $suff;
}

function BoyerMoore($pattern, $m, $text, $n, $status, $sentence)
{
    // $tanggal = date("Y-m-d H:i:s");
    $bmBc = PreBmBc($pattern, $m);
    $bmGs = PreBmGs($pattern, $m);
    $j = 0;
    $a = 1;
    while ($j <= $n - $m) {
        for ($i = $m - 1; $i >= 0 && $pattern[$i] == $text[$i + $j]; $i--) {
            $i--;
        }

        if ($i < 0) {
            echo "Pattern : $pattern";
            echo "<br>";
            echo "Ditemukan pada posisi pergeseran ke : $j";
            echo "<br>";
            echo "Status : $status";
            echo "<br>";
            
            // include'koneksi.php';
            // $histori = "INSERT INTO histori VALUES ('', '$sentence', '$text', '$pattern', '$j', '$tanggal')";
            // $con->query($histori);
            
            $j += $bmGs[0];
            
            if ($status == 'pagaron'){
                echo " motor maju";
                // include'koneksi.php';
                // $sql = "UPDATE iot SET text = '$text', status = '$status', status_iot = '1'  WHERE name_iot='pagar'";
                // $con->query($sql);

                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);

                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'on', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

	        else if($status == 'pagaroff'){
                echo "motor mundur";
                // include'koneksi.php';
                // $sql = "UPDATE iot SET text = '$text', status = '$status', status_iot = '0' WHERE name_iot='pagar'";
                // $con->query($sql);
                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'off', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

            else if($status == 'lampuon'){
                echo "lampu menyala";
                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'lampuon', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

            else if($status == 'lampuoff'){
                echo "lampu menyala";
                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'lampuoff', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

            else if($status == 'tvon'){
                echo "lampu menyala";
                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'tvon', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

            else if($status == 'tvoff'){
                echo "lampu menyala";
                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'tvoff', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

            else if($status == 'kipason'){
                echo "lampu menyala";
                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'kipason', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

            else if($status == 'kipasoff'){
                echo "lampu menyala";
                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'kipasoff', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

            else if($status == 'pompaon'){
                echo "lampu menyala";
                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'pompaon', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

            else if($status == 'pompaoff'){
                echo "lampu menyala";
                require('phpmqtt/phpMQTT.php');

                $server = '172.25.139.245';     // change if necessary
                $port = 1883;                     // change if necessary
                $username = '';                   // set your username
                $password = '';                   // set your password
                $client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

                $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


                if ($mqtt->connect(true, NULL, $username, $password)) {
                    $mqtt->publish('bluerhinos/phpMQTT/examples/pagar', 'pompaoff', 0, false);
                    $mqtt->close();
                } else {
                    echo "Time out!\n";
                }
            }

            echo "<br>";
            return $j;
            break;
        } else {
            $j += max($bmBc[$text[$i + $j]] - $m + 1 + $i, $bmGs[$i]);
        }
    }
}

$pagaron = 'pagaron';
$pagaroff = 'pagaroff';
$lampuon = 'lampuon';
$lampuoff = 'lampuoff';
$tvon = 'tvon';
$tvoff = 'tvoff';
$kipason = 'kipason';
$kipasoff = 'kipasoff';
$pompaon = 'pompaon';
$pompaoff = 'pompaoff';

$pattern1 = 'buka pagar';
$pattern2 = 'tutup pagar';
$pattern3 = 'pagar buka';
$pattern4 = 'pagar tutup';

$pattern5 = 'hidup lampu';
$pattern6 = 'mati lampu';
$pattern7 = 'lampu hidup';
$pattern8 = 'lampu mati';

$pattern9 = 'hidup tv';
$pattern10 = 'mati tv';
$pattern11 = 'tv hidup';
$pattern12 = 'tv mati';

$pattern13 = 'hidup kipas';
$pattern14 = 'mati kipas';
$pattern15 = 'kipas hidup';
$pattern16 = 'kipas mati';

$pattern17 = 'hidup pompa';
$pattern18 = 'mati pompa';
$pattern19 = 'pompa hidup';
$pattern20 = 'pompa mati';

$pattern21 = 'nyala lampu';
$pattern22 = 'lampu nyala';

$pattern23 = 'nyala tv';
$pattern24 = 'tv nyala';

$pattern25 = 'nyala kipas';
$pattern26 = 'kipas nyala';

$pattern27 = 'nyala pompa';
$pattern28 = 'pompa nyala';

BoyerMoore($pattern1, strlen($pattern1), $text, strlen($text), $pagaron, $sentence);
BoyerMoore($pattern2, strlen($pattern2), $text, strlen($text), $pagaroff, $sentence);
BoyerMoore($pattern3, strlen($pattern3), $text, strlen($text), $pagaron, $sentence);
BoyerMoore($pattern4, strlen($pattern4), $text, strlen($text), $pagaroff, $sentence);

BoyerMoore($pattern5, strlen($pattern5), $text, strlen($text), $lampuon, $sentence);
BoyerMoore($pattern6, strlen($pattern6), $text, strlen($text), $lampuoff, $sentence);
BoyerMoore($pattern7, strlen($pattern7), $text, strlen($text), $lampuon, $sentence);
BoyerMoore($pattern8, strlen($pattern8), $text, strlen($text), $lampuoff, $sentence);

BoyerMoore($pattern9, strlen($pattern9), $text, strlen($text), $tvon, $sentence);
BoyerMoore($pattern10, strlen($pattern10), $text, strlen($text), $tvoff, $sentence);
BoyerMoore($pattern11, strlen($pattern11), $text, strlen($text), $tvon, $sentence);
BoyerMoore($pattern12, strlen($pattern12), $text, strlen($text), $tvoff, $sentence);

BoyerMoore($pattern13, strlen($pattern13), $text, strlen($text), $kipason, $sentence);
BoyerMoore($pattern14, strlen($pattern14), $text, strlen($text), $kipasoff, $sentence);
BoyerMoore($pattern15, strlen($pattern15), $text, strlen($text), $kipason, $sentence);
BoyerMoore($pattern16, strlen($pattern16), $text, strlen($text), $kipasoff, $sentence);

BoyerMoore($pattern17, strlen($pattern17), $text, strlen($text), $pompaon, $sentence);
BoyerMoore($pattern18, strlen($pattern18), $text, strlen($text), $pompaoff, $sentence);
BoyerMoore($pattern19, strlen($pattern19), $text, strlen($text), $pompaon, $sentence);
BoyerMoore($pattern20, strlen($pattern20), $text, strlen($text), $pompaoff, $sentence);

BoyerMoore($pattern21, strlen($pattern21), $text, strlen($text), $lampuon, $sentence);
BoyerMoore($pattern22, strlen($pattern22), $text, strlen($text), $lampuon, $sentence);

BoyerMoore($pattern23, strlen($pattern23), $text, strlen($text), $tvon, $sentence);
BoyerMoore($pattern24, strlen($pattern24), $text, strlen($text), $tvon, $sentence);

BoyerMoore($pattern25, strlen($pattern25), $text, strlen($text), $kipason, $sentence);
BoyerMoore($pattern26, strlen($pattern26), $text, strlen($text), $kipason, $sentence);

BoyerMoore($pattern27, strlen($pattern27), $text, strlen($text), $pompaon, $sentence);
BoyerMoore($pattern28, strlen($pattern28), $text, strlen($text), $pompaon, $sentence);

$akhir = microtime(true);
$lama = $akhir - $awal;
echo "Lama eksekusi script adalah: ".$lama." microsecond";