pipeline {
    agent any

    stages {
        stage('Getting project from Git') {
            steps {
                checkout([$class: 'GitSCM', branches: [[name: '*/main']], extensions: [], userRemoteConfigs: [[url: 'https://github.com/ahmedebahrouni/BNAA.git']]])
            }
        }

        stage('Cleaning the project') {
            steps {
                sh "rm -rf var/cache/*"
            }
        }

 stage('Build Docker Image') {
                      steps {
                          script {
                            sh 'docker build -t ahmed1919/pfe:first .'
                          }
                      }
                  }

                  stage('login dockerhub') {
                                        steps {

				sh 'docker login -u ahmed1919 --password dckr_pat_YhUHa7uRtFGtL91LFwJ6nb0Mpgw'
                                            }
		  }

	                      stage('Push Docker Image') {
                                        steps {
                                   sh 'docker push ahmed1919/pfe:first'
                                            }
		  }



<<<<<<< HEAD
 stage('Build Docker Image') {
                      steps {
                          script {
                            sh 'docker build -t ahmed1919/pfe:first .'
                          }
                      }
                  }

                  stage('login dockerhub') {
                                        steps {

                   sh 'docker login -u ahmed1919 --password dckr_pat_wRsBljrIeVpG1l8CBB5TxXBXKqA'
                                            }
		  }

	                      stage('Push Docker Image') {
                                        steps {
                                   sh 'docker push ahmed1919/pfe:first'
                                            }
		  }



=======
>>>>>>> 4c46503ead20e1bcd64ff87fdcc804ffb155f50b


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
