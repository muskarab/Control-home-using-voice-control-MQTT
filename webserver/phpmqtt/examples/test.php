<?php

require('../phpMQTT.php');

$server = '172.25.139.245';     // change if necessary
$port = 1883;                     // change if necessary
$username = '';                   // set your username
$password = '';                   // set your password
$client_id = 'phpMQTT-publisher'; // make sure this is unique for connecting to sever - you could use uniqid()

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);


// include'koneksi.php';
// include composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// create stemmer
// cukup dijalankan sekali saja, biasanya didaftarkan di service container
$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
$stemmer  = $stemmerFactory->createStemmer();

// stem
// $sentence = $_GET['sentence'];
$sentence = 'bukakan pagar rumah';
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
            
            if ($status == 'buka'){
            echo " motor maju";
            // include'koneksi.php';
            // $sql = "UPDATE iot SET text = '$text', status = '$status', status_iot = '1'  WHERE name_iot='pagar'";
            // $con->query($sql);
		
	if ($mqtt->connect(true, NULL, $username, $password)) {
	$mqtt->publish('bluerhinos/phpMQTT/examples/test', 'off', 0, false);
	$mqtt->close();
	} else {
    	echo "Time out!\n";
	}

            } else {
                echo "motor mundur";
                // include'koneksi.php';
                // $sql = "UPDATE iot SET text = '$text', status = '$status', status_iot = '0' WHERE name_iot='pagar'";
                // $con->query($sql);
            }
            echo "<br>";
            return $j;
            break;
        } else {
            $j += max($bmBc[$text[$i + $j]] - $m + 1 + $i, $bmGs[$i]);
        }
    }
}

$buka = 'buka';
$tutup = 'tutup';

$pattern = 'buka pagar';
$pattern2 = 'tutup pagar';
// $pattern3 = 'pagar buka';
// $pattern4 = 'pagar tutup';
// $pattern3 = 'abc';
BoyerMoore($pattern, strlen($pattern), $text, strlen($text), $buka, $sentence);
BoyerMoore($pattern2, strlen($pattern2), $text, strlen($text), $tutup, $sentence);
// BoyerMoore($pattern3, strlen($pattern3), $text, strlen($text),$buka);
// BoyerMoore($pattern4, strlen($pattern4), $text, strlen($text), $tutup);