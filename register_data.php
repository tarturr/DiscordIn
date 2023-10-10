<?php

require 'user_data/DatabaseConnection.php';
require 'user_data/User.php';
require 'common/helpers.php';

$connection = new DatabaseConnection('localhost', 'root', '');

$dateFormatter = 'Y-m-d H:i:s';
$dateNow = new DateTime('now');
$strDateNow = $dateNow->format($dateFormatter);

$maxDate = $dateNow;
$maxDateInterval = new DateInterval('P15Y');
$maxDate->sub($maxDateInterval);

if (DateTime::createFromFormat($dateFormatter, $_POST['date_naiss']) > $maxDate) {
    sendToPageWithError('register.php', 'Vous êtes trop jeune pour accéder au site !<br/>Vous devez avoir 15 ans minimum.');
}

$utilisateur = new Utilisateur($connection, $_POST['pseudo'], $_POST['email'], $_POST['mot_de_passe'], $_POST['date_naiss'], $strDateNow, $strDateNow);

try {
    if (!$utilisateur->createInDatabase()) {
        sendToPageWithError('register.php', 'An error has occurred while sending data into the database.');
    } else {
        setcookie('pseudo', $utilisateur->pseudo, time() + 60 * 60);
        header('Location: welcome.php');
        exit();
    }
} catch (PDOException $error) {
    sendToPageWithError('register.php', $error->getMessage());
}