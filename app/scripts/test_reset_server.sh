#!/bin/sh

echo "_____________________________________________________________________________________"
echo ""
echo "[Automatique] Détruit les processus du serveur de test Panther 9080 et 4444"
echo "_____________________________________________________________________________________"
echo ""

if lsof -ti :9080 >/dev/null; then
    lsof -ti :9080 | xargs kill -9
    echo "Le processus utilisant le port 9080 a été détruit"
echo ""
else
    echo "Aucun processus n'utilise le port 9080"
echo ""
fi

if lsof -ti :4444 >/dev/null; then
    lsof -ti :4444 | xargs kill -9
    echo "Le processus utilisant le port 4444 a été détruit"
echo ""
else
    echo "Aucun processus n'utilise le port 4444"
echo ""
fi
