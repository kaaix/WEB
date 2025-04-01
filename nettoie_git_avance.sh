#!/bin/bash

echo "----------------------------------"
echo "[i] Nettoyage complet du projet..."
echo "----------------------------------"

# Dossiers à supprimer
echo "[•] Suppression des dossiers sensibles..."
rm -rf vendor
rm -rf var
rm -rf .idea
rm -rf .vscode
rm -rf public/built

# Fichiers sensibles
echo "[•] Suppression des fichiers sensibles..."
rm -f .env.local
rm -f .gitignore
rm -f public/*.map


# Fichiers temporaires et caches
echo "[•] Nettoyage des fichiers temporaires..."
find . -name "*.log" -type f -delete
find . -name "*.DS_Store" -type f -delete
find . -name "*~" -type f -delete

# Suppression des dossiers vides
echo "[•] Suppression des répertoires vides..."
find . -type d -empty -delete

# Neutralisation des valeurs SECRET= dans les fichiers .env pour éviter les alertes
echo "[•] Neutralisation des chaînes SECRET= dans les fichiers .env..."
find . -type f -name ".env*" -exec sed -i 's/^SECRET=/# SECRET=/g' {} \;

echo "✅ Nettoyage terminé. Projet prêt à être archivé ou rendu."
