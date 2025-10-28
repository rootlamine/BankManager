pipeline {
    // Exécute le pipeline sur n'importe quel agent (votre machine Jenkins locale)
    agent any

    // Définition de toutes les variables et des références aux IDs des Credentials
    environment {
        DOCKER_IMAGE = 'rootbaabel/bankmanager'

        // Version de base SemVer : Démarre à v1.0.1 car v1.0.0 existe déjà
        BASE_VERSION = 'v1.0.1'

        // Tag unique et traçable (Ex: v1.0.1-1, v1.0.1-2, etc.)
        // BUILD_NUMBER est une variable automatique fournie par Jenkins
        IMAGE_TAG = "${BASE_VERSION}-${BUILD_NUMBER}"

        // IDs des Credentials Jenkins (Doivent correspondre exactement aux IDs configurés)
        DOCKERHUB_CREDENTIALS_ID = 'docker-hub-credentials'
        RENDER_WEBHOOK_SECRET_ID = 'render-deploy-webhook'
    }

    stages {
        stage('Build & Tag Image') {
            steps {
                echo "--- 1. Building Docker image with unique tag: ${IMAGE_TAG} ---"

                // Construit l'image en utilisant le Dockerfile et applique le tag unique
                sh "docker build -t ${DOCKER_IMAGE}:${IMAGE_TAG} ."

                // Tag la même image avec le tag 'latest'
                echo "2. Tagging the new image as :latest"
                sh "docker tag ${DOCKER_IMAGE}:${IMAGE_TAG} ${DOCKER_IMAGE}:latest"
            }
        }

        stage('Run Tests (Placeholder)') {
            steps {
                echo '--- 3. SKIPPING TESTS ---'
                // les tests avant l'étape de PUSH
            }
        }

        stage('Push to Docker Hub') {
            steps {
                // Déverrouille les identifiants pour l'authentification sécurisée
                withCredentials([usernamePassword(
                    credentialsId: env.DOCKERHUB_CREDENTIALS_ID,
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    echo "--- 4. Connecting and Pushing to Docker Hub ---"
                    // Authentification via les variables sécurisées
                    sh "docker login -u ${DOCKER_USER} -p ${DOCKER_PASS}"

                    // Push du tag unique (pour l'historique)
                    sh "docker push ${DOCKER_IMAGE}:${IMAGE_TAG}"

                    // Push du tag latest (pour le déploiement sur Render)
                    sh "docker push ${DOCKER_IMAGE}:latest"
                }
            }
        }

        stage('Deploy to Render') {
            steps {
                // Déverrouille l'URL Webhook stockée comme Secret Text
                withCredentials([string(
                    credentialsId: env.RENDER_WEBHOOK_SECRET_ID,
                    variable: 'RENDER_WEBHOOK_URL'
                )]) {
                    echo "--- 5. Triggering Render deployment via Webhook ---"
                    // Envoi du signal POST à Render. Render va chercher l'image :latest.
                    sh "curl -X POST -H 'Content-Type: application/json' ${RENDER_WEBHOOK_URL}"
                }
            }
        }
    }
}
