def COLOR_MAP = [          //Define variables for post build - slack notification
    'SUCCESS': 'good',
    'FAILURE': 'danger',
]


pipeline {
    agent any
    tools {
	    maven "MAVEN3"         //add tools
	    jdk "OracleJDK11"
	}

    environment {
        registryCredential = 'ecr:us-east-1:awscreds'
        appRegistry = "116594513860.dkr.ecr.us-east-1.amazonaws.com/l00179719apprep"
        l00179719Registry = "https://116594513860.dkr.ecr.us-east-1.amazonaws.com/"
        cluster = "l00179719cluster"
        service = "l00179719svc"    //service to run the ecs tasks for project
        
    }
  stages {
    stage('Fetch Git code'){
      steps {
        git branch: 'main', url: 'https://github.com/L00179719/mypipeline.git'
      }
    }


    stage('Build code'){
            steps {
                sh 'mvn install -DskipTests'
            }

            post {
                success {
                    echo 'Archiving artifacts'
                    archiveArtifacts artifacts: '**/*.war'
                }
            }
        }

    stage('Maven Test'){  
      steps {
        sh 'mvn test'
      }
    }

    stage ('Code analysis with checkstyle Maven'){
            steps {
                sh 'mvn checkstyle:checkstyle'
            }
            post {
                success {
                    echo 'Generated Analysis Result'
                }
            }
        }

    stage('Sonar Analysis') {
            environment {
                scannerHome = tool 'sonar4.7'
            }
            steps {
               withSonarQubeEnv('sonar') {
                   sh '''${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=l00179719 \
                   -Dsonar.projectName=l00179719 \
                   -Dsonar.projectVersion=1.0 \
                   -Dsonar.sources=src/ \
                   -Dsonar.java.binaries=target/test-classes/com/visualpathit/account/controllerTest/ \
                   -Dsonar.junit.reportsPath=target/surefire-reports/ \
                   -Dsonar.jacoco.reportsPath=target/jacoco.exec \
                   -Dsonar.java.checkstyle.reportPaths=target/checkstyle-result.xml'''
              }
            }
        }


    stage("Quality Gate") {
            steps {
                timeout(time: 1, unit: 'MINUTES') {
                    // Parameter indicates whether to set pipeline to UNSTABLE if Quality Gate fails
                    // true = set pipeline to UNSTABLE, false = don't
                    waitForQualityGate abortPipeline: true
                }
            }
        }



    stage('Build App Image') {
       steps {
       
         script {
                dockerImage = docker.build(appRegistry+ ":$BUILD_NUMBER",".")
             }

     }
    
    }

    stage('Upload App Image') {
          steps{
            script {
              docker.withRegistry( l00179719Registry, registryCredential ) {
                dockerImage.push("$BUILD_NUMBER")
                dockerImage.push('latest')
              }
            }
          }
        }
    stage('Deploy to ecs') {
          steps {
        withAWS(credentials: 'awscreds', region: 'us-east-1') {
          sh 'aws ecs update-service --cluster ${cluster} --service ${service} --force-new-deployment'
        }
      }
     }
    }
       

post {
        always {
            echo 'Slack Notifications'
            slackSend channel: 'jenkinscicd',
                color: COLOR_MAP[currentBuild.currentResult],
                message: "*${currentBuild.currentResult}:* Job ${env.JOB_NAME} build ${env.BUILD_NUMBER} \n"
        }
    } 
     
}