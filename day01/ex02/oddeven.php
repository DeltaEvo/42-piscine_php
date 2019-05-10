#!/usr/bin/env php
<?php
while (true) {
    echo "Entrez un nombre: ";
    if (!($input = fgets(STDIN)))
        break;
    $input = trim($input);
    if (is_numeric($input))
        echo "Le chiffre $input est ", $input % 2 ? "Impair" : "Pair", "\n";
    else
        echo "'$input' n'est pas un chiffre\n";
}
echo "\n";