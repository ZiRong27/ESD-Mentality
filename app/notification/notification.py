from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy.exc import SQLAlchemyError
from flask_cors import CORS
from os import environ #For docker use
import threading

import json
import sys
import os
import uuid
import csv

# Communication patterns:
# Use a message-broker with 'topic' exchange to enable interaction
import pika

# External API used
from twilio.rest import Client

# Flask and database settings
app = Flask(__name__)
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root:root@localhost:8889/esd_notification'
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


'''
def receiveMessageTask():
    # prepare a queue for receiving messages
    channelqueue = channel.queue_declare(queue="notification", durable=True) # 'durable' makes the queue survive broker restarts so that the messages in it survive broker restarts too
    queue_name = channelqueue.method.queue
    channel.queue_bind(exchange=exchangename, queue=queue_name, routing_key='*.message') # bind the queue to the exchange via the key
        # any routing_key with two words and ending with '.message' will be matched
    
    # set up a consumer and start to wait for coming messages
    #channel.basic_qos(prefetch_count=1) # The "Quality of Service" setting makes the broker distribute only one message to a consumer if the consumer is available (i.e., having finished processing and acknowledged all previous messages that it receives)
    channel.basic_consume('notification',
                      callback,
                      auto_ack=True)
    #channel.basic_consume(queue=queue_name, on_message_callback=callback, auto_ack=True) # 'auto_ack=True' acknowledges the reception of a message to the broker automatically, so that the broker can assume the message is received and processed and remove it from the queue
    channel.start_consuming() # an implicit loop waiting to receive messages; it doesn't exit by default. Use Ctrl+C in the command window to terminate it.
'''

# Send a message to Patient MS to get the phone number
# Communication Pattern: Request-Reply
# Patient_id and message_to_send will be stored in the db while waiting for a reply
def get_phone_number(patient_id, message_to_send):
    # prepare message
    message = {"patient_id": patient_id}
    message=json.dumps(message, default=str)

    # create correlation id for reply
    corrid = str(uuid.uuid4())

    # add to database
    data_to_db = {"patient_id": patient_id, "correlation_id": corrid, "message": message_to_send}
    notification = Notification(**data_to_db)
    try:
        print (data_to_db   )
        db.session.add(notification)
        db.session.commit()
    except SQLAlchemyError as e:
        error = str(e.__dict__['orig'])
        print (error)
        return 'error'
    
    # channel settings
    url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
    params = pika.URLParameters(url)
    connection = pika.BlockingConnection(params)
    channel = connection.channel()

    # set up the exchange if the exchange doesn't exist
    exchangename="mentality"
    channel.exchange_declare(exchange=exchangename, exchange_type='topic')
    channel.queue_declare(queue='notification', durable=True) # make sure the queue used by Shipping exist and durable
    channel.queue_bind(exchange=exchangename, queue='notification', routing_key='notification.phoneNo.patient') # make sure the queue is bound to the exchange
    
    # publish
    channel.basic_publish(exchange=exchangename, routing_key="notification.phoneNo.patient", body=message,
    properties=pika.BasicProperties(delivery_mode = 2, # make message persistent within the matching queues until it is received by some receiver (the matching queues have to exist and be durable and bound to the exchange, which are ensured by the previous two api calls)
        reply_to="notification.reply.phoneNumber", # set the reply queue which will be used as the routing key for reply messages
        correlation_id=corrid # set the correlation id for easier matching of replies
    )
)


# def append_message_with_mentality_signature(msg): 
#     append_at_start = "[MENTALITY NOTIFICATION SERVICE] "
#     return append_at_start + msg

# def send_sms (message_org, to_phone_no):
#     #account_sid = os.environ.get('TWILIO_ACCOUNT_SID')
#     #auth_token = os.environ.get('TWILIO_AUTH_TOKEN')
#     account_sid = "ACfab1bfc1ce6dcc55d394818c7810c1d8"
#     auth_token = "7ff29283f2032a9411f05424f79d1af7"
#     client = Client(account_sid, auth_token)

#     from_phone_no = '+13018613110'
#     message = client.messages \
#           .create(
#              body=message_org,
#              from_= from_phone_no,
#              to=to_phone_no
#          ) 
#     #print("Sent message:", message)
#     print ('Message Sent to: ', to_phone_no, '! Message content: ', message_org, ' Message sid:', message.sid)

def start_consuming_messages():
    # channel settings
    url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
    params = pika.URLParameters(url)
    connection = pika.BlockingConnection(params)
    # hostname = "localhost" # default hostname
    # port = 5672 # default port
    # connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))

    channel = connection.channel()

    # set up the exchange if the exchange doesn't exist
    exchangename="mentality"
    channel.exchange_declare(exchange=exchangename, exchange_type='topic')

    # prepare a queue for receiving messages
    channelqueue = channel.queue_declare(queue="notification", durable=True) # 'durable' makes the queue survive broker restarts so that the messages in it survive broker restarts too
    queue_name = channelqueue.method.queue
    channel.queue_bind(exchange=exchangename, queue=queue_name, routing_key='*.message') # bind the queue to the exchange via the key

    # set up a consumer and start to wait for coming messages
    channel.basic_qos(prefetch_count=1) # The "Quality of Service" setting makes the broker distribute only one message to a consumer if the consumer is available (i.e., having finished processing and acknowledged all previous messages that it receives)
    channel.basic_consume(queue=queue_name, on_message_callback=callback, auto_ack=True)

    # start consuming
    print(' [*] This is notification.py waiting for messages:')

    channel.start_consuming() # an implicit loop waiting to receive messages; it doesn't exit by default. Use Ctrl+C in the command window to terminate it.
    # connection.close()


def callback(channel, method, properties, body): # required signature for the callback; no return
    result = json.loads(body) # json expected {patient_id, message}
    print ("receive message", result)
    try:
        get_phone_number(result["patient_id"], result["message"])
    except:
        print ("There is an error in data type of message")



# # Access the CLODUAMQP_URL environment variable and parse it 
# url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
# params = pika.URLParameters(url)
# connection = pika.BlockingConnection(params)
# hostname = "localhost" # default hostname
# port = 5672 # default port
# # connect to the broker and set up a communication channel in the connection
# # connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))

# channel = connection.channel() # start a channel
# # set up the exchange if the exchange doesn't exist
# exchangename="appointment_topic"
# channel.exchange_declare(exchange=exchangename, exchange_type='topic')
# channelqueue = channel.queue_declare(queue="notification", durable=True) # 'durable' makes the queue survive broker restarts so that the messages in it survive broker restarts too
# queue_name = channelqueue.method.queue
# channel.queue_bind(exchange=exchangename, queue=queue_name, routing_key='*.message') # bind the queue to the exchange via the key


# channel.basic_consume('notification',
#                       callback,
#                       auto_ack=True)

# # print(' [*] This is notification.py waiting for messages:')
# channel.start_consuming()

if __name__ == '__main__':
    
    # create a seperate thread to run receive patient details which is an infinite loop
    t1 = threading.Thread(target=start_consuming_messages)
    t1.start()
    app.run(host='0.0.0.0', port=5008, debug=True)



