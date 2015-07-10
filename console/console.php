<?php
echo 'Welcome to my console' . PHP_EOL;
$exit = false;

while (!$exit) {
    $handle = fopen('php://stdin', 'r');
    $line = fgets($handle);

    $exit = handleInput($line);
    echo 'You entered: ' . $line . PHP_EOL;
}

exit (0);

function handleInput($command)
{
    $exit = false;

    if (trim($command) === 'exit') {
        $exit = true;
    }

    return $exit;
}
