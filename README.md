Mentality
======
Mentality is a booking enterprise solution where patients can book consultations with the therapists at our therapy booking web application. It is an online website that easily and conveniently connects people with our therapists based on their needs, with our doctors’ details such as specialisations and fees explicitly stated on the website. 
The solution consists of 6 microservices - Patient, Doctor, Notification, Appointment, Consultation, Payment Payment Microservice uses Stripe External API and Notification Microservice uses Twilio External API. 



## Setup
1. Ensure you WAMP is running

2. Connect your WAMP to the ESD-ClinicAppointmentServices folder by creating an alias

3. The following microservices (doctor, patient, appointment, consultation, payment) are running on cloud, hence there is no need to run it individually. 
(However, you may also choose to run it locally, but do take note that you are require to run the micro-services on your system.) 

	If you choose to run locally the micro-services, they can be found at the following sub-folder.
		
		Doctor: ESD-ClinicAppointmentServices\app\doctor.py
		Patient: ESD-ClinicAppointmentServices\app\patient.py
		Appointment: ESD-ClinicAppointmentServices\app\appointment.py
		Consultation: ESD-ClinicAppointmentServices\app\consultation.py
		Payment: ESD-ClinicAppointmentServices\app\payment

	The following files are required to be run locally, and can be found at the following sub-folder.

		notification: ESD-ClinicAppointmentServices\app\notification
		notification_reply: ESD-ClinicAppointmentServices\app\notification	
		patient_amqp: ESD-ClinicAppointmentServices\app\patient

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
     $cfg['Servers'][$i]['verbose'] = 'Production OS_ticket';
     $cfg['Servers'][$i]['host'] = 'g2t1.cxwx5nwwwt9h.ap-southeast-1.rds.amazonaws.com';
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

7.  Alternatively, you can login using these account details

   - Patient - Username: **sophieng** Password: **sn1**
   - Patient - Username: **zoeytan** Password: **zt1**
   - Doctor - Username: **johnsmith** Password: **js1**
   - Doctor - Username: **rosafernandez** Password: **rf1**

You can now access our web application :)

Note: We have deployed all our microservice on AWS ECS and used Cloud RabbitMQ for messaging. Should you want to implement it on your own, you may refer to the guide below. 



## Guide

* see [SETUP CLOUD RABBITMQ](https://github.com/syafiqahmr/ESD-ClinicAppointmentServices/blob/master/SETUP_CLOUDRABBITMQ.md) file

* see [SETUP DOCKER CLOUD](https://github.com/syafiqahmr/ESD-ClinicAppointmentServices/blob/master/SETUP_DOCKERCLOUD.md) file

  

## Contributors

### Contributors on GitHub
* [Syafiqah](https://github.com/syafiqahmr)
* [Zi Rong](https://github.com/ZiRong27)
* [Sheng Qin](https://github.com//simshengqin)
* [Rachel](https://github.com/racheltoh)
* [Vittorio](https://github.com/[VittorioWeiLong](https://github.com/VittorioWeiLong))

## Version 
* Version 1.0
