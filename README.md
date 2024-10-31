

# Liste des extensions VSCode à installer
$extensions = @(
    "eamodio.gitlens",
    "GitHub.vscode-pull-request-github",
    "bmewburn.vscode-intelephense-client",
    "amiralizadeh9480.laravel-extra-intellisense",
    "onecentlin.laravel-blade",
    "shufo.vscode-blade-formatter",
    "mikestead.dotenv",
    "EditorConfig.EditorConfig",
    "usernamehw.errorlens",
    "oderwat.indent-rainbow",
    "Gruntfuggly.todo-tree",
    "naumovs.color-highlight",
    "PKief.material-icon-theme",
    "ms-azuretools.vscode-docker",
    "ms-vscode-remote.remote-containers"
)

# Installation des extensions
foreach ($extension in $extensions) {
    code --install-extension $extension
}


docker compose up -d


# Pour installer les dépendances
docker compose exec app composer install

# Pour copier le .env
docker compose exec app cp .env.example .env

# Pour générer la clé
docker compose exec app php artisan key:generate

# Pour les permissions
docker compose exec app chown -R www-data:www-data /var/www/storage
docker compose exec app chmod -R 775 /var/www/storage


# Pour nettoyer les caches
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear
docker compose exec app php artisan cache:clear

# Pour les migrations
docker compose exec app php artisan migrate


# Pour les logs :
docker compose exec app cat storage/logs/laravel.log
