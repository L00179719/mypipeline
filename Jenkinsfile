pipeline {
    agent any
    tools {
	    maven "MAVEN3"
	    jdk "OracleJDK11"
	}

    environment {
        registryCredential = 'ecr:us-east-1:awscreds'
        appRegistry = "116594513860.dkr.ecr.us-east-1.amazonaws.com/l00179719apprep"
        vprofileRegistry = "https://116594513860.dkr.ecr.us-east-1.amazonaws.com/"
        //cluster = "l00179719cluster"
        //service = "l00179719service"
        
    }
  stages {
    stage('Fetch Git code'){
      steps {
        git branch: 'main', url: 'https://github.com/L00179719/mypipeline.git'
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
              docker.withRegistry( vprofileRegistry, registryCredential ) {
                dockerImage.push("$BUILD_NUMBER")
                dockerImage.push('latest')
              }
            }
          }
        }
    
    }    

    
     
}