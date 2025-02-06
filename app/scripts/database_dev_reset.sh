#!/bin/sh

PROJECT_ROOT=$(dirname "$(dirname "$(realpath "$0")")")

. "$PROJECT_ROOT/scripts/script_utils.sh"

echo "_____________________________________________________________________________________"
echo ""
echo "[Dev] Reset de la base de données de développement"
echo "_____________________________________________________________________________________"
echo ""

logStepStart "[1/5] Destruction de la base de données"
php bin/console d:d:d --force --if-exists --env=dev ||
    handleError "Échec de la destruction de la base de données"
logStepEnd

logStepStart "[2/5] Construction de la base de données"
php bin/console d:d:c --env=dev ||
    handleError "Échec de la création de la base de données"
logStepEnd

logStepStart "[3/5] Exécution des migrations"
php bin/console d:m:m -n --env=dev ||
    handleError "Échec des migrations"
logStepEnd

logStepStart "[4/5] Exécution des fixtures"
php bin/console d:f:l -n --append --group=dev --env=dev ||
    handleError "Échec de l'exécution des fixtures"
logStepEnd

logStepStart "[5/5] Mise à jour du cache"
php bin/console c:c --env=dev ||
    handleError "Échec de la mise à jour du cache"
logStepEnd
