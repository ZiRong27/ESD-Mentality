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

