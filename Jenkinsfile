// Laravel multi-environment deploy: development / staging / main (production)
// Jenkins builds and tests. The server only receives the finished package over SSH.
//
// Safety rules built into this file:
//   - shared/.env and shared/storage are never touched or overwritten
//   - APP_ENV in the server .env must match the target (stops wrong-env deploys)
//   - production database is backed up before migrations
//   - the live switch is atomic, and a failed deploy/health check rolls back automatically
//   - the remote release preparation/switch operation is serialized with flock

pipeline {
    agent any

    options {
        timestamps()
        disableConcurrentBuilds()
        buildDiscarder(logRotator(numToKeepStr: '15', artifactNumToKeepStr: '3'))
        timeout(time: 45, unit: 'MINUTES')
    }

    environment {
        // ---- EDIT THESE ----
        DEPLOY_HOST             = 'YOUR_SERVER_IP'
        DEPLOY_USER             = 'deploy'
        SSH_CRED_ID             = 'kvm8-ssh-key'
        PHP_FPM_SVC             = 'php8.3-fpm'
        APP_NAME                = 'comfortair'
        KEEP_RELEASES           = '5'
        HEALTH_PATH             = '/up'     // Laravel 12 native health endpoint
        BACKUP_DB_ON_PRODUCTION = 'true'    // mysqldump before migrations (production only)

        // ---- keep as is ----
        SSH_OPTS = '-o StrictHostKeyChecking=yes -o BatchMode=yes -o ConnectTimeout=15 -o ServerAliveInterval=30'
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
                script {
                    env.GIT_SHORT = sh(returnStdout: true, script: 'git rev-parse --short HEAD').trim()
                }
                sh '''
                    echo "Branch: ${BRANCH_NAME:-n/a}"
                    echo "Commit: ${GIT_SHORT}"
                    php -v
                '''
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                    set -eu

                    test -f composer.json

                    composer install \
                        --prefer-dist \
                        --no-interaction \
                        --no-progress

                    if [ -f package.json ]; then
                        if [ -f package-lock.json ]; then
                            npm ci
                        else
                            npm install
                        fi
                    fi
                '''
            }
        }

        stage('Test') {
            steps {
                sh '''
                    set -eu

                    if [ -f artisan ]; then
                        if [ -f .env.testing ]; then
                            echo "Using .env.testing"
                        elif [ ! -f .env ] && [ -f .env.example ]; then
                            echo "Creating build .env from .env.example"
                            cp .env.example .env
                            php artisan key:generate --no-interaction || true
                        fi

                        php artisan test
                    fi
                '''
            }
        }

        stage('Build Frontend') {
            steps {
                sh '''
                    set -eu

                    if [ -f package.json ]; then
                        if npm run | grep -q ' build'; then
                            npm run build
                        else
                            echo "No build script found; skipping frontend build."
                        fi
                    fi
                '''
            }
        }

        // Everything below runs only for development, staging and main.
        // Feature branches and PR builds stop after the tests (no red build).
        stage('Release') {
            when {
                anyOf {
                    branch 'development'
                    branch 'staging'
                    branch 'main'
                }
            }

            stages {

                stage('Select Environment') {
                    steps {
                        script {
                            switch (env.BRANCH_NAME) {
                                case 'development':
                                    env.DEPLOY_PATH = '/var/www/comfortair-development'
                                    env.APP_URL     = 'https://dev.example.com'
                                    env.TARGET_ENV  = 'development'
                                    break

                                case 'staging':
                                    env.DEPLOY_PATH = '/var/www/comfortair-staging'
                                    env.APP_URL     = 'https://staging.example.com'
                                    env.TARGET_ENV  = 'staging'
                                    break

                                case 'main':
                                    env.DEPLOY_PATH = '/var/www/comfortair-production'
                                    env.APP_URL     = 'https://example.com'
                                    env.TARGET_ENV  = 'main'
                                    break

                                default:
                                    error("Deployment is not configured for branch: ${env.BRANCH_NAME}")
                            }

                            // Unique, sortable release name (never reused, even if the job is recreated)
                            def ts = sh(returnStdout: true, script: 'date -u +%Y%m%d%H%M%S').trim()
                            env.RELEASE_ID = "${ts}-${env.BUILD_NUMBER}"

                            env.BACKUP_DB = (env.TARGET_ENV == 'production' && env.BACKUP_DB_ON_PRODUCTION == 'true') ? 'true' : 'false'

                            echo "Target environment: ${env.TARGET_ENV}"
                            echo "Deployment path:    ${env.DEPLOY_PATH}"
                            echo "Release id:         ${env.RELEASE_ID}"
                            echo "DB backup:          ${env.BACKUP_DB}"
                        }
                    }
                }

                stage('Prepare Deployment Package') {
                    steps {
                        sh '''
                            set -eu

                            # Clear build/test cache so it never ships to servers
                            php artisan config:clear || true
                            php artisan route:clear || true
                            php artisan view:clear || true

                            composer install \
                                --no-dev \
                                --prefer-dist \
                                --optimize-autoloader \
                                --no-interaction \
                                --no-progress

                            tar -czf deployment.tar.gz \
                                --exclude='./deployment.tar.gz' \
                                --exclude='./.git' \
                                --exclude='./.env*' \
                                --exclude='.env*' \
                                --exclude='./bootstrap/cache/*.php' \
                                --exclude='./node_modules' \
                                --exclude='./tests' \
                                --exclude='./storage/logs/*' \
                                --exclude='./storage/framework/cache/*' \
                                --exclude='./storage/framework/sessions/*' \
                                --exclude='./storage/framework/views/*' \
                                .
                        '''
                        archiveArtifacts artifacts: 'deployment.tar.gz', fingerprint: true
                    }
                }

                // Production approval gate: main branch only.
                stage('Production Deployment Approval') {
                    when {
                        branch 'main'
                    }

                    options {
                        timeout(time: 15, unit: 'MINUTES')
                    }

                    steps {
                        input(
                            id: 'ProductionApproval',
                            message: "Deploy commit ${env.GIT_SHORT} from '${env.BRANCH_NAME}' to PRODUCTION (${env.APP_URL})?\nDatabase backup before migration: ${env.BACKUP_DB}",
                            ok: 'Deploy to production'
                        )

                        echo 'Production deployment approved.'
                    }
                }

                stage('Deploy') {
                    steps {
                        script { env.DEPLOY_STARTED = 'true' }

                        sshagent(credentials: [env.SSH_CRED_ID]) {
                            sh '''
                                set -eu

                                REMOTE="${DEPLOY_USER}@${DEPLOY_HOST}"
                                PKG="/tmp/${APP_NAME}-${RELEASE_ID}.tar.gz"

                                echo "Uploading package..."
                                scp ${SSH_OPTS} deployment.tar.gz "${REMOTE}:${PKG}"

                                echo "Releasing ${RELEASE_ID} to ${DEPLOY_PATH} (${TARGET_ENV})..."
                                ssh ${SSH_OPTS} "${REMOTE}" "APP='${DEPLOY_PATH}' REL='${RELEASE_ID}' PKG='${PKG}' FPM='${PHP_FPM_SVC}' TARGET_ENV='${TARGET_ENV}' BACKUP_DB='${BACKUP_DB}' bash -s" <<'REMOTE_SCRIPT'
set -euo pipefail
umask 002

ENV_FILE="$APP/shared/.env"
RELEASE_DIR="$APP/releases/$REL"
CREATED=no
SWITCHED=no

cleanup() {
    rm -f "$PKG"
    # Remove a half-built release, but never one that went live
    if [ "$CREATED" = "yes" ] && [ "$SWITCHED" = "no" ]; then
        rm -rf "$RELEASE_DIR"
    fi
}
trap cleanup EXIT

# Read one value from this project's .env
envval() {
    awk -F= -v k="$1" '$1==k { sub(/^[^=]*=/, ""); print; exit }' "$ENV_FILE" | tr -d '[:cntrl:]' | sed -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"
}

# ---- Safety checks (nothing is changed yet) ----
[ -f "$ENV_FILE" ] || { echo "ERROR: $ENV_FILE not found. Create it first."; exit 1; }
[ -d "$APP/shared/storage" ] || { echo "ERROR: $APP/shared/storage not found."; exit 1; }

if [ -e "$APP/current" ] && [ ! -L "$APP/current" ]; then
    echo "ERROR: $APP/current exists but is not a symlink. Convert it manually first."
    exit 1
fi

ENV_APP_ENV=$(envval APP_ENV)
if [ "$ENV_APP_ENV" != "$TARGET_ENV" ]; then
    echo "ERROR: APP_ENV is '$ENV_APP_ENV' in $ENV_FILE but this deploy targets '$TARGET_ENV'. Stopping to protect data."
    exit 1
fi

if [ "$TARGET_ENV" = "production" ] && [ "$(envval APP_DEBUG)" != "false" ]; then
    echo "ERROR: APP_DEBUG must be false in production."
    exit 1
fi

# One deploy at a time per project
exec 9>"$APP/shared/.deploy.lock"
flock -n 9 || { echo "ERROR: another deployment is already running for $APP."; exit 1; }

mkdir -p "$APP/releases"
if [ -e "$RELEASE_DIR" ]; then
    echo "ERROR: $RELEASE_DIR already exists. Refusing to overwrite."
    exit 1
fi

# ---- Unpack ----
mkdir "$RELEASE_DIR"
CREATED=yes
tar -xzf "$PKG" -C "$RELEASE_DIR"
cd "$RELEASE_DIR"

# Link shared files (these live outside the release and are never overwritten)
ln -sfn "$ENV_FILE" .env
rm -rf storage
ln -s "$APP/shared/storage" storage
mkdir -p "$APP/shared/storage/app/public"
rm -rf public/storage
ln -s "$APP/shared/storage/app/public" public/storage
mkdir -p bootstrap/cache

# PHP version / extensions must match what vendor/ was built for
if [ -f vendor/composer/platform_check.php ]; then
    php vendor/composer/platform_check.php
fi

# ---- Database backup (production) ----
if [ "$BACKUP_DB" = "true" ]; then
    DB_CONN=$(envval DB_CONNECTION)
    case "$DB_CONN" in
        mysql|mariadb) ;;
        *) echo "ERROR: automatic backup supports mysql/mariadb only (found '$DB_CONN')."; exit 1 ;;
    esac
    command -v mysqldump >/dev/null 2>&1 || { echo "ERROR: mysqldump is not installed on the server."; exit 1; }

    DB_HOST_V=$(envval DB_HOST); DB_HOST_V=${DB_HOST_V:-127.0.0.1}
    DB_PORT_V=$(envval DB_PORT); DB_PORT_V=${DB_PORT_V:-3306}

    mkdir -p "$APP/shared/backups"
    chmod 700 "$APP/shared/backups"
    BACKUP_FILE="$APP/shared/backups/db-$REL.sql.gz"

    echo "Backing up database before migration..."
    ( umask 077; MYSQL_PWD="$(envval DB_PASSWORD)" mysqldump --single-transaction --quick --no-tablespaces -h "$DB_HOST_V" -P "$DB_PORT_V" -u "$(envval DB_USERNAME)" "$(envval DB_DATABASE)" | gzip > "$BACKUP_FILE" )
    [ -s "$BACKUP_FILE" ] || { echo "ERROR: backup file is empty."; exit 1; }
    echo "Backup saved: $BACKUP_FILE"

    # keep the 10 newest backups
    ls -1t "$APP/shared/backups"/db-*.sql.gz | tail -n +11 | xargs -r rm -f --
fi

# ---- Laravel ----
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ---- Atomic switch ----
if [ -L "$APP/current" ]; then
    readlink -f "$APP/current" > "$APP/shared/previous-release"
fi

ln -sfn "$RELEASE_DIR" "$APP/current.new"
mv -Tf "$APP/current.new" "$APP/current"
SWITCHED=yes

sudo -n systemctl reload "$FPM"
php "$APP/current/artisan" queue:restart || true

echo "Release $REL is live."
REMOTE_SCRIPT
                            '''
                        }
                    }
                }

                stage('Health Check') {
                    steps {
                        sshagent(credentials: [env.SSH_CRED_ID]) {
                            sh '''
                                set -eu

                                ssh ${SSH_OPTS} "${DEPLOY_USER}@${DEPLOY_HOST}" "URL='${APP_URL}${HEALTH_PATH}' bash -s" <<'REMOTE_SCRIPT'
set -u
for i in 1 2 3 4 5 6; do
    code=$(curl -s -o /dev/null -w '%{http_code}' --location --max-time 15 "$URL" || true)
    echo "Health check attempt $i: HTTP $code"
    case "$code" in
        2*) echo "Health check passed."; exit 0 ;;
    esac
    sleep 5
done
echo "ERROR: health check failed."
exit 1
REMOTE_SCRIPT
                            '''
                        }
                        script { env.HEALTHY = 'true' }
                    }
                }

                // A problem here must not turn a good deploy red (and trigger a rollback)
                stage('Cleanup Old Releases') {
                    steps {
                        catchError(buildResult: 'SUCCESS', stageResult: 'UNSTABLE') {
                            sshagent(credentials: [env.SSH_CRED_ID]) {
                                sh '''
                                    ssh ${SSH_OPTS} "${DEPLOY_USER}@${DEPLOY_HOST}" "APP='${DEPLOY_PATH}' KEEP='${KEEP_RELEASES}' bash -s" <<'REMOTE_SCRIPT'
set -euo pipefail
cd "$APP/releases"

CUR=$(readlink -f "$APP/current" || true)
PREV=$(cat "$APP/shared/previous-release" 2>/dev/null || true)

# Delete releases older than the newest KEEP, but never the live or the previous one
ls -1dt */ | tail -n +$((KEEP + 1)) | while IFS= read -r d; do
    d=${d%/}
    TARGET=$(readlink -f "$APP/releases/$d")
    if [ "$TARGET" = "$CUR" ] || [ "$TARGET" = "$PREV" ]; then
        continue
    fi
    echo "Removing old release $d"
    rm -rf -- "$APP/releases/$d"
done
REMOTE_SCRIPT
                                '''
                            }
                        }
                    }
                }
            }
        }
    }

    post {
        success {
            echo "Pipeline completed successfully for ${env.BRANCH_NAME}."
        }

        // Runs for failed AND aborted builds. It does nothing unless this build's
        // release is the one currently live and it never passed the health check.
        unsuccessful {
            script {
                if (env.DEPLOY_STARTED == 'true' && env.HEALTHY != 'true') {
                    echo 'Deploy did not finish cleanly. Checking whether a rollback is needed...'
                    try {
                        sshagent(credentials: [env.SSH_CRED_ID]) {
                            sh '''
                                ssh ${SSH_OPTS} "${DEPLOY_USER}@${DEPLOY_HOST}" "APP='${DEPLOY_PATH}' REL='${RELEASE_ID}' FPM='${PHP_FPM_SVC}' bash -s" <<'REMOTE_SCRIPT'
set -euo pipefail

EXPECT=$(readlink -f "$APP/releases/$REL" 2>/dev/null || echo "$APP/releases/$REL")
CUR=$(readlink -f "$APP/current" 2>/dev/null || true)

if [ "$CUR" != "$EXPECT" ]; then
    echo "Live release is not build $REL. No rollback needed."
    exit 0
fi

PREV=$(cat "$APP/shared/previous-release" 2>/dev/null || true)
if [ -z "$PREV" ] || [ ! -d "$PREV" ]; then
    echo "ERROR: no previous release to roll back to. Manual action required."
    exit 1
fi

ln -sfn "$PREV" "$APP/current.new"
mv -Tf "$APP/current.new" "$APP/current"
sudo -n systemctl reload "$FPM"
php "$APP/current/artisan" queue:restart || true
echo "Rolled back to $PREV"
REMOTE_SCRIPT
                            '''
                        }
                    } catch (err) {
                        echo "ROLLBACK FAILED: ${err.getMessage()} - check the server manually!"
                    }
                }
            }
        }

        // 'cleanup' always runs last, after the rollback above
        cleanup {
            sh 'rm -f deployment.tar.gz || true'
            cleanWs()
        }
    }
}



