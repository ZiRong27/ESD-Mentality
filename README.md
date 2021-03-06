Mentality
======
Link: https://github.com/ZiRong27/ESD-Mentality

Mentality is a booking enterprise solution where patients can book consultations with the therapists at our therapy booking web application. It is an online website that easily and conveniently connects people with our therapists based on their needs, with our doctors’ details such as specialisations and fees explicitly stated on the website. 

The solution consists of 6 microservices - Patient, Doctor, Notification, Appointment, Consultation, Payment Payment Microservice uses Stripe External API and Notification Microservice uses Twilio External API. 



## Setup
1. Ensure you WAMP is running

2. Connect your WAMP to the ESD-ClinicAppointmentServices folder by creating an alias

3. The following microservices (doctor, patient, appointment, consultation, payment) are running on cloud, hence there is no need to run it individually. 

   - However, you may also choose to run it locally, but do take note that you are require to run the micro-services on your system. 

   - If you choose to run locally the micro-services, they can be found at the following sub-folder.

     ```bash
     Doctor: ESD-ClinicAppointmentServices\docker\doctor
     Patient: ESD-ClinicAppointmentServices\docker\patient
     Appointment: ESD-ClinicAppointmentServices\docker\appointment
     Consultation: ESD-ClinicAppointmentServices\docker\consultation
     Payment: ESD-ClinicAppointmentServices\docker\payment
     Notification: ESD-ClinicAppointmentServices\docker\notification
     ```

   - Then, go to C:\wamp64\www\ESD-ClinicAppointmentServices\app\ui\include\codeLinks.php, comment line 31-35 and uncomment line 37-41 to switch to localhost ip.

4. The database is also running on cloud, hence there is no need to import the database. (However, should you want to look into the database structure, you may look at the ESD-ClinicAppointmentServices/sql folder)

5. To access the web application you may go to the following link

   - **Mentality Landing Page**: localhost:80/ESD-ClinicAppointmentServices/app/ui/landing 
   - **Mentality Landing Page**: localhost:80/ESD-ClinicAppointmentServices/app/ui/landing/index.php
   - **Mentality Patient Portal**: localhost:80/ESD-ClinicAppointmentServices/app/ui
   - **Mentality Doctor Portal**: localhost:80/ESD-ClinicAppointmentServices/app/ui/doctor/doctorLogin.php

6. To access our online RDS database through phpmyadmin, follow these steps

   - Navigate to config.inc file. It is normally in this location: C:\wamp64\apps\phpmyadmin4.8.3\config.inc.php

   - Insert the following code at the very bottom, one line before ?>

     ```sql
     $i++;
     $cfg['Servers'][$i]['verbose'] = 'ESD';
     $cfg['Servers'][$i]['host'] = 'esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com';
     $cfg['Servers'][$i]['port'] = '3306';
     $cfg['Servers'][$i]['socket'] = '';
     $cfg['Servers'][$i]['connect_type'] = 'tcp';
     $cfg['Servers'][$i]['extension'] = 'mysql';
     $cfg['Servers'][$i]['compress'] = TRUE;
     $cfg['Servers'][$i]['auth_type'] = 'cookie';
     $cfg['Servers'][$i]['auth_type'] = 'config';
     $cfg['Servers'][$i]['user'] = 'admin';
     $cfg['Servers'][$i]['password'] = 'IloveESMandPaul!<3';
	  ```

   - Go to http://localhost/phpmyadmin/index.php

   - Select ESD(admin) as the server choice. Username: root password: (leave it empty) 

   - Login. The tables are in ESD, namely esd_appointment, esd_consultation, esd_doctor, esd_notification, esd_patient, esd_payment

7. Alternatively, you can login using these account details

   - Patient - Username: **sophieng** Password: **sn1**
   - Patient - Username: **zoeytan** Password: **zt1**
   - Doctor - Username: **johnsmith** Password: **js1**
   - Doctor - Username: **rosafernandez** Password: **rf1**

You can now access our web application :)

Note: We have deployed all our microservice on AWS ECS and used Cloud RabbitMQ for messaging. Should you want to implement it on your own, you may refer to the guide below. 



## Setup Cloud RabbitMQ

1. Go to cloudamqp.com

2. Create an account and sign in

3. Click “Create Instance” button

4. Put ESD for instance name. Leave the plan as “Little Lemur (Free)”

5. Click select region

6. Select AP-Southeast-2(Sydney) for Data Center and click review

7. Click create instance

8. Click on the created instance

9. Copy the AMQP url

10. Put the below command in all the microservices that need to connect to this cloud AMQP. The rest of the code will be the standard rabbitMQ code. 

    ```python
    url = <Insert your AMQP url here>
    params = pika.URLParameters(url)
    connection = pika.BlockingConnection(params)
    ```



## Build Docker Images and Push To AWS ECS Cloud

1. Open command prompt

2. Run these commands:

   ```bash
    pip install awscli
   ```

3. Allow root access to awscli by running these commands:

   ```bash
   aws configureAWS Access Key ID [************]: <Removed for confidentiality>
   AWS Secret Access Key [************]: <Removed for confidentiality>
   Default region name [ap-southeast-1]: ap-southeast-1
   Default output format [json]: json
   ```

   

4. Build docker images by running: (Note that you need to change the path to the current location of all the microservices). You will also need to delete any existing images (NOT repositories) manually first in AWS before pushing

   ```bash
   cd C:\wamp64\www\ESD-ClinicAppointmentServices\docker\dockerfiles\patient
   docker build -t g6t8/patient .
   
   cd C:\wamp64\www\ESD-ClinicAppointmentServices\docker\dockerfiles\doctor
   docker build -t g6t8/doctor .
   
   cd C:\wamp64\www\ESD-ClinicAppointmentServices\docker\dockerfiles\appointment
   docker build -t g6t8/appointment .
   
   cd C:\wamp64\www\ESD-ClinicAppointmentServices\docker\dockerfiles\consultation
   docker build -t g6t8/consultation .
   
   cd C:\wamp64\www\ESD-ClinicAppointmentServices\docker\dockerfiles\payment
   docker build -t g6t8/payment .
   
   cd C:\wamp64\www\ESD-ClinicAppointmentServices\docker\dockerfiles\notification
   docker build -t g6t8/notification .
   
   cd C:\wamp64\www\ESD-ClinicAppointmentServices\docker\dockerfiles\patient
   docker build -t g6t8/patient_amqp .
   
   cd C:\wamp64\www\ESD-ClinicAppointmentServices\docker\dockerfiles\notification
   docker build -t g6t8/notification_reply .
   ```

   

5. Go to AWS account => ECS service => Repositories (On the left side)

6. Click create repository => Name it as g6t8/patient

7. Repeat previous step for the 7 other microservices: g6t8/doctor, g6t8/appointment, g6t8/consultation, g6t8/notification, g6t8/payment, g6t8/patient_amqp, g6t8/notification_reply.

8. Click on patient repository => View push command

9. Copy the code in Step 1 and run it to authenticate your Docker client to your registry. It should be something like 

   ```bash
   aws ecr get-login-password --region ap-southeast-1 | docker login --username AWS --password-stdin <Removed for confidentiality>
   ```

10. Run the commands in Step 3 and 4 to push the docker images to this repository. It should be something like docker tag g6t8/patient:latest and docker push …. /g6t8/patient:latest

11. Repeat previous step for the 5 other microservices. Below are the commands that we used for reference:

    ```bash
    docker tag g6t8/patient:latest 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/patient:latest
    docker push 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/patient:latest
    
    docker tag g6t8/doctor:latest 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/doctor:latest
    docker push 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/doctor:latest
    
    docker tag g6t8/appointment:latest 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/appointment:latest
    docker push 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/appointment:latest
    
    docker tag g6t8/consultation:latest 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/consultation:latest
    docker push 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/consultation:latest
    
    docker tag g6t8/notification:latest 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/notification:latest
    docker push 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/notification:latest
    
    docker tag g6t8/payment:latest 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/payment:latest
    docker push 603184320246.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/payment:latest
    
    docker tag g6t8/patient_amqp:latest 558294856729.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/patient_amqp:latest
    docker push 558294856729.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/patient_amqp:latest
    
    docker tag g6t8/notification_reply:latest 558294856729.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/notification_reply:latest
    docker push 558294856729.dkr.ecr.ap-southeast-1.amazonaws.com/g6t8/notification_reply:latest
    ```

    

12. Go back to AWS ECS => repositories (On the left navigation bar) Click on each of the 6 repositories and ensure all the images are pushed into their respective repo. 

13. Copy the URI of g6t8/patient repository. Note that it is the repo URI, NOT the images URI (Should not have :latest behind)

14. Click on Clusters on the left navigation bar. Click “Get Started”

15. Click the “Configure” button under custom

16. Put “patient” for container name. 

17. Paste the URI for g6t8/patient repo you have copied earlier into the image box

18. Change the port mapping to 80 tcp18. Click update => Next.

19. Do NOT change the load balancer type, leave it as none. Click Next again. 

20. Put “patient” for the cluster name and click Next

21. Review your settings and click create. 

22. Wait for your cluster to finish creating. (All green ticks) 

23. Click on your created cluster. (Alternatively, click on Clusters on the left navigation bar => click on Patient)

24. Click on tasks. There should be 1 task running. Click on its task id (beside the task definition)

25. Click on the ENI id.

26. Scroll to the right until you see the Security groups column. Click on it.

27. Click on the security group ID

28. Click edit inbound rules

29. Click add rule. Change the type to all traffic and source to anywhere. Click save rules

30. Go back to the task page for the patient cluster (Should be in another tab if you have not closed it yet)

31. Copy the public IP. This will be the IP you use to connect to this patient microservice. 

32. Repeat step 14 to 31 for all the other 7 repositories (each for 1 microservice)

## Contributors

### Contributors on GitHub
* [Syafiqah](https://github.com/syafiqahmr)
* [Zi Rong](https://github.com/ZiRong27)
* [Sheng Qin](https://github.com//simshengqin)
* [Rachel](https://github.com/racheltoh)
* [Vittorio](https://github.com/[VittorioWeiLong](https://github.com/VittorioWeiLong))

## Version 
* Version 1.0
