#!/bin/bash

PROJECT_ROOT=$(dirname "$(dirname "$(realpath "$0")")")

. "$PROJECT_ROOT/scripts/script_utils.sh"

RunGroupTestsWithCover() {
    local group_name="$1"

    local coverage_suffix="coverage-${group_name}"
    local coverage_file="build/temp/${coverage_suffix}.cov"

    XDEBUG_MODE=coverage php bin/phpunit --group "$group_name" --coverage-php "$coverage_file" || {
        handleError "Échec lors de l'exécution des tests pour le groupe '$group_name'"
    }
}

echo "_____________________________________________________________________________________"
echo ""
echo "[Test complet] Exécution des tests unitaires, fonctionnels et qualités"
echo ""
echo "[Info] L'exécution des tests peut prendre plusieurs minutes"
echo "_____________________________________________________________________________________"
echo ""

./scripts/test_reset_server.sh

logStepStart "[1/13] Destruction de la base de données"
php bin/console d:d:d --force --env=test ||
    handleError "Échec de la destruction de la base de données"
logStepEnd

logStepStart "[2/13] Construction de la base de données"
php bin/console d:d:c --env=test ||
    handleError "Échec de la création de la base de données"
logStepEnd

logStepStart "[3/13] Mise à jour du shéma"
php bin/console doctrine:schema:create --env=test ||
    handleError "Échec de la mise à jour du schéma"
logStepEnd

logStepStart "[4/13] Exécution des fixtures"
php bin/console d:f:l -n --append --group=test --env=test ||
    handleError "Échec de l'exécution des fixtures"
logStepEnd

logStepStart "[5/13] Mise à jour du cache"
php bin/console c:c --env=test || handleError "Échec de la mise à jour du cache"
logStepEnd

logStepStart "[6/13] Test unitaires"
RunGroupTestsWithCover "unit"
logStepEnd

logStepStart "[7/13] Test E2E des pages d'authentification"
RunGroupTestsWithCover "E2E_auth"
RunGroupTestsWithCover "E2E_auth_password"
logStepEnd

logStepStart "[8/13] Test E2E des pages communes"
RunGroupTestsWithCover "E2E_app"
logStepEnd

logStepStart "[9/13] Test E2E des pages de profil"
RunGroupTestsWithCover "E2E_profile"
logStepEnd

logStepStart "[10/13] Test E2E des pages produits"
RunGroupTestsWithCover "E2E_product"
logStepEnd

logStepStart "[11/13] Test E2E des pages d'administration d'une boutique"
RunGroupTestsWithCover "E2E_admin_shop_read"
RunGroupTestsWithCover "E2E_admin_shop_edit"
RunGroupTestsWithCover "E2E_admin_shop_delete"
logStepEnd

logStepStart "[12/13] Test E2E des pages de drops d'une boutique"
RunGroupTestsWithCover "E2E_admin_shop_wave_collection"
RunGroupTestsWithCover "E2E_admin_shop_wave_create"
RunGroupTestsWithCover "E2E_admin_shop_wave_create_and_draft"
RunGroupTestsWithCover "E2E_admin_shop_wave_create_and_publish"
RunGroupTestsWithCover "E2E_admin_shop_wave_edit"
RunGroupTestsWithCover "E2E_admin_shop_wave_edit_and_draft"
RunGroupTestsWithCover "E2E_admin_shop_wave_edit_and_publish"
RunGroupTestsWithCover "E2E_admin_shop_wave_delete"
logStepEnd

logStepStart "[13/13] Fusion des couvertures de tests"
php tests/MergeCoverage.php || handleError "Échec lors de la fusion des couvertures de tests"
logStepEnd

echo "_____________________________________________________________________________________"
echo ""
echo "[Test complet succès] Exécution des tests unitaires, fonctionnels et qualités"
echo ""
echo "[Couverture] Couverture du code disponible dans 'build/coverage-html/index.html'"
echo "_____________________________________________________________________________________"
echo ""
