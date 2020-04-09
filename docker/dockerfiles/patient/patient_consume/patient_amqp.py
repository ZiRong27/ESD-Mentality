from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from os import environ #For docker use
import threading

from datetime import datetime
import json
import pika
import os

app = Flask(__name__)
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_patient'
#app.config['SQLALCHEMY_DATABASE_URI'] = environ.get('dbURL')
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://admin:IloveESMandPaul!<3@esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com/esd_patient'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
 
db = SQLAlchemy(app)
CORS(app)

hostname = "localhost" # default hostname
port = 5672 # default port
# connect to the broker and set up a communication channel in the connection
connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))
    # Note: various network firewalls, filters, gateways (e.g., SMU VPN on wifi), may hinder the connections;
    # If "pika.exceptions.AMQPConnectionError" happens, may try again after disconnecting the wifi and/or disabling firewalls

# channel settings
url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
params = pika.URLParameters(url)
connection = pika.BlockingConnection(params)
channel = connection.channel()

# set up the exchange if the exchange doesn't exist
exchangename="patient_details"
channel.exchange_declare(exchange=exchangename, exchange_type='topic')


class Patient(db.Model):
    __tablename__ = 'patient'
    patient_id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String, nullable=False)
    gender = db.Column(db.String, nullable=False)
    dob = db.Column(db.String, nullable=False)
    phone = db.Column(db.String, nullable=False)
    salutation  = db.Column(db.String, nullable=False)
    username = db.Column(db.String, nullable=False)
    password = db.Column(db.String, nullable=False)   

    def json(self):
        dto = {
            'patient_id': self.patient_id, 
            'name': self.name,
            'gender' : self.gender ,
            'dob' : self.dob ,
            'phone' : self.phone ,
            'salutation' : self.salutation ,
            'username' : self.username ,
            'password' : self.password 
        }
        return dto  


def find_phone(patient_id):
    patient = Patient.query.filter_by(patient_id=patient_id).first()
    if patient:
        return patient.phone
    return None

# AMQP
# get phone number from patient microservice
def receive_patient_details():
    # prepare a queue for receiving messages
    channelqueue = channel.queue_declare(queue="patient", durable=True) # 'durable' makes the queue survive broker restarts so that the messages in it survive broker restarts too
    queue_name = channelqueue.method.queue
    channel.queue_bind(exchange=exchangename, queue=queue_name, routing_key='*.phone_no.patient') # bind the queue to the exchange via the key
    
    # set up a consumer and start to wait for coming messages
    channel.basic_qos(prefetch_count=1) # The "Quality of Service" setting makes the broker distribute only one message to a consumer if the consumer is available (i.e., having finished processing and acknowledged all previous messages that it receives)
    channel.basic_consume(queue=queue_name, on_message_callback=callback, auto_ack=True) # 'auto_ack=True' acknowledges the reception of a message to the broker automatically, so that the broker can assume the message is received and processed and remove it from the queue
    print("waiting for messages")
    channel.start_consuming() # an implicit loop waiting to receive messages; it doesn't exit by default. Use Ctrl+C in the command window to terminate it.

def callback(channel, method, properties, body): # required signature for the callback; no return
    
    print("Received request for patient's details")
    
    # retrieve results and find phone no.
    result = json.loads(body) 
    phone = find_phone(result["patient_id"])
    
    # prepare message
    message = {"phone": phone}
    message=json.dumps(message, default=str)
    print (message)

    
    channel.queue_declare(queue='patient', durable=True) # make sure the queue used by Shipping exist and durable
    channel.queue_bind(exchange=exchangename, queue='patient', routing_key='notification.reply.phone_number') # make sure the queue is bound to the exchange
    channel.basic_publish(exchange=exchangename,
        routing_key=properties.reply_to, # use the reply queue set in the request message as the routing key for reply messages
        body=message, 
        properties=pika.BasicProperties(delivery_mode = 2, # make message persistent (stored to disk, not just memory) within the matching queues; default is 1 (only store in memory)
            correlation_id = properties.correlation_id, # use the correlation id set in the request message
        )
    )


if __name__ == '__main__':

    # create a seperate thread to run receive patient details which is an infinite loop
    t1 = threading.Thread(target=receive_patient_details)
    t1.start()

    app.run(host='0.0.0.0', port=5009, debug=True)