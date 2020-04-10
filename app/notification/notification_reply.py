from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from os import environ #For docker use
import threading

import sys
import os
import csv

import pika
import json

# External API used
from twilio.rest import Client


# Flask and database settings
app = Flask(__name__)
# app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_notification'
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://admin:IloveESMandPaul!<3@esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com/esd_notification'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)
CORS(app)

class Notification(db.Model):
    __tablename__ = 'notification'
    patient_id = db.Column(db.Integer, primary_key=True)
    correlation_id = db.Column(db.String, nullable=False)
    message = db.Column(db.String, nullable=False)

    def __init__(self, patient_id, correlation_id, message):
        # sets the properties (of itself when created)
        self.patient_id = patient_id
        self.correlation_id = correlation_id
        self.message = message

    def json(self):
        dto = {
            'patient_id': self.patient_id, 
            'correlation_id': self.correlation_id,
            'message' : self.message ,
        }
        return dto  

def receivePatientPhone():
    # channel settings
    url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
    params = pika.URLParameters(url)
    connection = pika.BlockingConnection(params)
    channel = connection.channel()

    # set up the exchange if the exchange doesn't exist
    exchangename="mentality"
    channel.exchange_declare(exchange=exchangename, exchange_type='topic')
    channel.queue_declare(queue='notification', durable=True) # make sure the queue used by Shipping exist and durable
    channel.queue_bind(exchange=exchangename, queue='notification', routing_key='notification.reply.phoneNumber') # make sure the queue is bound to the exchange
    
    # set up a consumer and start to wait for coming messages
    channel.basic_qos(prefetch_count=1) # The "Quality of Service" setting makes the broker distribute only one message to a consumer if the consumer is available (i.e., having finished processing and acknowledged all previous messages that it receives)
    channel.basic_consume(queue='notification',
            on_message_callback=reply_callback, # set up the function called by the broker to process a received message
    ) # prepare the reply_to receiver
    channel.start_consuming() # an implicit loop waiting to receive messages; it doesn't exit by default. Use Ctrl+C in the command window to terminate it.

def reply_callback(channel, method, properties, body): # required signature for a callback; no return
    """processing function called by the broker when a message is received"""
    
    notification = Notification.query.filter_by(correlation_id=properties.correlation_id).first()

    if notification:
        result = json.loads(body)
        print (result)
        try:
            phone_no = result["phone"]
            phone_no = "+65" + phone_no
            send_sms(notification.message, phone_no)

            # delete correlation id from the database
            db.session.delete(notification)
        except:
            print ("There is an error in message data")

    else:
        print ("no matching correlation id")

    # acknowledge to the broker that the processing of the message is completed
    channel.basic_ack(delivery_tag=method.delivery_tag)


def append_message_with_mentality_signature(msg): 
    append_at_start = "[MENTALITY NOTIFICATION SERVICE] "
    return append_at_start + msg

def send_sms (message_org, to_phone_no):
    #account_sid = os.environ.get('TWILIO_ACCOUNT_SID')
    #auth_token = os.environ.get('TWILIO_AUTH_TOKEN')
    message_org = append_message_with_mentality_signature(message_org)
    account_sid = "ACfab1bfc1ce6dcc55d394818c7810c1d8"
    auth_token = "7ff29283f2032a9411f05424f79d1af7"
    client = Client(account_sid, auth_token)

    from_phone_no = '+13018613110'
    message = client.messages \
          .create(
             body=message_org,
             from_= from_phone_no,
             to=to_phone_no
         ) 
    #print("Sent message:", message)
    print ('Message Sent to: ', to_phone_no, '! Message content: ', message_org, ' Message sid:', message.sid)


# Execute this program if it is run as a main script (not by 'import')
if __name__ == '__main__':
    
    # create a seperate thread to run receive patient details which is an infinite loop
    t1 = threading.Thread(target=receivePatientPhone)
    t1.start()

    app.run(host='0.0.0.0', port=5007, debug=True)
