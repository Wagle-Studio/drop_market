#!/bin/sh

PROJECT_ROOT=$(dirname "$(dirname "$(realpath "$0")")")

. "$PROJECT_ROOT/scripts/script_utils.sh"

echo "_____________________________________________________________________________________"
echo ""
echo "[test unique] Exécution de tests ciblés avec PHPUnit et Panther"
echo "_____________________________________________________________________________________"
echo ""

logStepStart "[1/2] Choisissez une option :"
echo "1. Exécuter un dossier spécifique"
echo "2. Exécuter un fichier spécifique"
echo "3. Exécuter une classe spécifique"
echo "______________________________________________"
echo ""
echo "4. Tests E2E des pages d'authentification"
echo "5. Tests E2E des pages communes"
echo "6. Tests E2E des pages de profil"
echo "7. Tests E2E des pages produits"
echo "8. Tests E2E des pages d'administration d'une boutique"
echo "9. Tests E2E des pages de drops d'une boutique"
echo "______________________________________________"
echo ""
echo "10. Tests unitaires complets"
echo "______________________________________________"
echo ""
echo "q. Quitter"
logStepEnd
read -p "Votre choix : " CHOICE

./scripts/test_reset_server.sh

case $CHOICE in
1)
    logStepStart "[2/2] Exécuter un dossier spécifique"
    read -p "Entrez le chemin du dossier de test (ex: tests/Unit/) : " FOLDER
    if [ -d "$PROJECT_ROOT/$FOLDER" ]; then
        php bin/phpunit "$FOLDER" ||
            handleError "Échec lors de l'exécution du dossier de test : $PROJECT_ROOT/$FOLDER"
    else
        handleError "Le dossier spécifié n'existe pas : $PROJECT_ROOT/$FOLDER"
    fi
    ;;
2)
    logStepStart "[2/2] Exécuter un fichier spécifique"
    read -p "Entrez le chemin du fichier de test (ex: tests/Unit/MonTest.php) : " FILE
    if [ -f "$PROJECT_ROOT/$FILE" ]; then
        php bin/phpunit "$FILE" ||
            handleError "Échec lors de l'exécution du fichier : $PROJECT_ROOT/$FILE"
    else
        handleError "Le fichier spécifié n'existe pas : $PROJECT_ROOT/$FILE"
    fi
    ;;
3)
    logStepStart "[2/2] Exécuter une classe spécifique"
    read -p "Entrez le nom de la classe à tester (ex: MonTest) : " CLASS
    if [ ! -z "$CLASS" ]; then
        php bin/phpunit --filter $CLASS ||
            handleError "Échec lors de l'exécution des tests pour la classe : $CLASS"
    else
        handleError "Aucune classe n'existe avec nom spécifié : $CLASS"
    fi
    ;;
4)
    logStepStart "[2/2] Exécution des tests E2E des pages d'authentification"
    php bin/phpunit --group E2E_auth --testdox &&
    php bin/phpunit --group E2E_auth_password --testdox ||
        handleError "Échec lors de l'exécution des tests E2E des pages d'authentification"
    ;;
5)
    logStepStart "[2/2] Exécution des tests E2E des pages communes"
    php bin/phpunit --group E2E_app --testdox ||
        handleError "Échec lors de l'exécution des tests E2E des pages communes"
    ;;
6)
    logStepStart "[2/2] Exécution des tests E2E des pages de profil"
    php bin/phpunit --group E2E_profile --testdox ||
        handleError "Échec lors de l'exécution des tests E2E des pages de profil"
    ;;
7)
    logStepStart "[2/2] Exécution des tests E2E des pages produits"
    php bin/phpunit --group E2E_product --testdox ||
        handleError "Échec lors de l'exécution des tests E2E des pages produits"
    ;;
8)
    logStepStart "[2/2] Exécution des test E2E des pages d'administration d'une boutique"
    php bin/phpunit --group E2E_admin_shop_read --testdox &&
    php bin/phpunit --group E2E_admin_shop_edit --testdox &&
    php bin/phpunit --group E2E_admin_shop_delete --testdox ||
        handleError "Échec lors de l'exécution des test E2E des pages d'administration d'une boutique"
    ;;
9)
    logStepStart "[2/2] Exécution des tests E2E des pages de drops d'une boutique"
    php bin/phpunit --group E2E_admin_shop_wave_collection --testdox &&
    php bin/phpunit --group E2E_admin_shop_wave_create --testdox &&
    php bin/phpunit --group E2E_admin_shop_wave_create_and_draft --testdox &&
    php bin/phpunit --group E2E_admin_shop_wave_create_and_publish --testdox &&
    php bin/phpunit --group E2E_admin_shop_wave_edit --testdox &&
    php bin/phpunit --group E2E_admin_shop_wave_edit_and_draft --testdox &&
    php bin/phpunit --group E2E_admin_shop_wave_edit_and_publish --testdox &&
    php bin/phpunit --group E2E_admin_shop_wave_delete --testdox ||
        handleError "Échec lors de l'exécution des tests E2E des pages de drops d'une boutique"
    ;;
10)
    logStepStart "[2/2] Exécution des tests unitaires complets"
    php bin/phpunit --group unit --testdox ||
        handleError "Échec lors de l'exécution des tests unitaires complets"
    ;;
q)
    quitScript
    ;;
*)
    echo "[Erreur] Option invalide. Veuillez relancer le script."
    exit 1
    ;;
esac
