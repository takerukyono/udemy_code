<?php
$points = [10, 20, 30];
$total = 0;

for ($i=0; $i<count($points); $i++) {
    $total += $points[$i];
}

echo $total . PHP_EOL;