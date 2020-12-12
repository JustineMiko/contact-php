
<?php

use \DateTime;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

require __DIR__.'/vendor/autoload.php';

// instanciation du chargeur de templates
$loader = new FilesystemLoader(__DIR__.'/templates');

// instanciation du moteur de template
$twig = new Environment($loader, [
    // activation du mode debug
    'debug' => true,
    // activation du mode de variables strictes
    'strict_variables' => true,
]);

// chargement de l'extension DebugExtension
$twig->addExtension(new DebugExtension());

// traitement des données
$formData = [
    'email' => '',
    'subject' => '',
    'message' => '',
];
$errors = [];

if ($_POST) {
    foreach ($formData as $key => $value) {
        if (isset($_POST[$key])) {
            $formData[$key] = $_POST[$key];
        }
    }

    $minLength = 3;
    $maxLength = 190;
    $maxLengthText = 1000;

//champ email :
    if (empty($_POST['email'])) {
        $errors['email'] = 'merci d\'indiquer votre adresse email';
    } elseif (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
        $errors['email'] = 'merci de renseigner un email valide';
    } elseif (strlen($_POST['email']) > $maxLength) {
        $errors['email'] = "votre email est trop long, il doit comprendre moins de {$maxLength} caractères";
    }
//champ objet : 
    if (empty($_POST['subject'])) {
        // le champs est-il vide ?
        $errors['subject'] = 'Vous devez préciser l\'objet de votre message';
    } elseif (strlen($_POST['subject']) < $minLength || strlen($_POST['subject']) > $maxLength) {
        // la longueur du mail est-elle hors des limites ?
        $errors['subject'] = "merci de renseigner un objet dont la longueur est comprise entre {$minLength} et {$maxLength} inclus";
    } elseif (preg_match('/^[a-zA-Z]+$/', $_POST['subject']) === 0) {
        // le login est-il composé exclusivement de lettres de a à z, majuscules ou mnisucules ?
        $errors['subject'] = 'merci de renseigner un objet composé uniquement de lettres de l\'alphabet sans accent';
    }

//champ message:
    if (empty($_Post['message'])) {
        //le champ est-il vide ? 
        $errors['message'] = 'Vous devez entrer un message';
    } elseif (strlen($_POST['message'] < $minLength || strlen($_POST['message']) > $maxLengthText) {
    // la longueur du message est-elle comprise entre 3 et 1000 caractères ?
        $errors['message'] = "Votre message doit comprendre entre {$minLength} et {$maxLengthText} caractères"
    }


    // si il n'y a pas d'erreur, on redirige l'utilisateur vers la page d'accueil
    if (!$errors) {
        $url = '/';
        header("Location: {$url}", true, 302);
        exit();
    }
}

// affichage du rendu d'un template
echo $twig->render('contact.html.twig', [
    // transmission de données au template
    'errors' => $errors,
    'formData' => $formData,
]);