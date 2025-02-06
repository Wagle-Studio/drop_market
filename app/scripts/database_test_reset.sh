#!/bin/sh

PROJECT_ROOT=$(dirname "$(dirname "$(realpath "$0")")")

. "$PROJECT_ROOT/scripts/script_utils.sh"

echo "_____________________________________________________________________________________"
echo ""
echo "[Test] Reset de la base de données de test"
echo "_____________________________________________________________________________________"
echo ""

logStepStart "[1/5] Destruction de la base de données"
php bin/console d:d:d --force --env=test ||
    handleError "Échec de la destruction de la base de données"
logStepEnd

logStepStart "[2/5] Construction de la base de données"
php bin/console d:d:c --env=test ||
    handleError "Échec de la création de la base de données"
logStepEnd

logStepStart "[3/5] Mise à jour du shéma"
php bin/console doctrine:schema:create --env=test ||
    handleError "Échec de la mise à jour du schéma"
logStepEnd

logStepStart "[4/5] Exécution des fixtures"
php bin/console d:f:l -n --append --group=test --env=test ||
    handleError "Échec de l'exécution des fixtures"
logStepEnd

logStepStart "[5/5] Mise à jour du cache"
php bin/console c:c --env=test ||
    handleError "Échec de la mise à jour du cache"
logStepEnd
