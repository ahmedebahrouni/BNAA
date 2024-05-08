pipeline {
    agent any

    stages {
        stage('Getting project from Git') {
            steps {
                checkout([$class: 'GitSCM', branches: [[name: '*/master']], extensions: [], userRemoteConfigs: [[url: 'https://github.com/ahmedebahrouni/Peoject.git']]])
            }
        }

        stage('Cleaning the project') {
            steps {
                sh "rm -rf var/cache/*"
            }
        }

        stage('Composer Install') {
            steps {
                sh "composer install --no-interaction --no-progress --prefer-dist"
            }
        }

        stage('Run Symfony Unit Tests') {
            steps {
                sh "php bin/phpunit"
            }
        }

        stage('Run Symfony Code Quality Check via SonarQube') {
            steps {
                      sh " mvn clean verify sonar:sonar -Dsonar.projectKey=BNA -Dsonar.projectName='BNA' -Dsonar.host.url=http://192.168.33.10:9000 -Dsonar.token=sqp_02d5e42e73eb7b1c470f0142d92e39d1b93351d8"

            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    sh 'docker build -t yourusername/your-symfony-app .'
                }
            }
        }

        stage('Push Docker Image') {
            steps {
                script {
                    sh 'docker login -u yourusername --password yourdockerhubpassword'
                    sh 'docker push yourusername/your-symfony-app'
                }
            }
        }
    }

    post {
        success {
            mail bcc: '', body: 'Morning, Your Symfony pipeline build was successful. Thanks for your time!', from: 'jenkins@example.com', subject: 'Build Finished - Success', to: 'your.email@example.com'
        }
        failure {
            mail bcc: '', body: 'Dear user, Your Symfony pipeline build failed. Keep working!', from: 'jenkins@example.com', subject: 'Build Finished - Failure', to: 'your.email@example.com'
        }
        always {
            emailext attachLog: true, body: '', subject: 'Build finished', from: 'jenkins@example.com', to: 'your.email@example.com'
            cleanWs()
        }
    }
}
