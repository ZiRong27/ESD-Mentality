#!/usr/bin/env python3
# The above shebang (#!) operator tells Unix-like environments
# to run this file as a python3 script

import json
import sys
import os
from os import environ

# Communication patterns:
# Use a message-broker with 'topic' exchange to enable interaction
import pika

# External API used
from twilio.rest import Client



hostname = "localhost" # default hostname
port = 5672 # default port
# connect to the broker and set up a communication channel in the connection
connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))
channel = connection.channel()
# set up the exchange if the exchange doesn't exist
exchangename="appointment_topic"
channel.exchange_declare(exchange=exchangename, exchange_type='topic')

# set up queue to receive message task
# routing key = '*.message'
# queue = notification
def receiveMessageTask():
    # prepare a queue for receiving messages
    channelqueue = channel.queue_declare(queue="notification", durable=True) # 'durable' makes the queue survive broker restarts so that the messages in it survive broker restarts too
    queue_name = channelqueue.method.queue
    channel.queue_bind(exchange=exchangename, queue=queue_name, routing_key='*.message') # bind the queue to the exchange via the key
        # any routing_key with two words and ending with '.message' will be matched
    
    # set up a consumer and start to wait for coming messages
    channel.basic_qos(prefetch_count=1) # The "Quality of Service" setting makes the broker distribute only one message to a consumer if the consumer is available (i.e., having finished processing and acknowledged all previous messages that it receives)
    channel.basic_consume(queue=queue_name, on_message_callback=callback, auto_ack=True) # 'auto_ack=True' acknowledges the reception of a message to the broker automatically, so that the broker can assume the message is received and processed and remove it from the queue
    channel.start_consuming() # an implicit loop waiting to receive messages; it doesn't exit by default. Use Ctrl+C in the command window to terminate it.


def callback(channel, method, properties, body): # required signature for the callback; no return
    result = processMessageTask(json.loads(body)) # json expected {phone_no, message}


def processMessageTask(task): # handles task of sending sms notification
    phone_no = task["phone"]
    message = append_message_with_mentality_signature(task["message"])
    
    send_sms(message, phone_no)

def append_message_with_mentality_signature(msg): 
    append_at_start = "[MENTALITY NOTIFICATION SERVICE] "
    return append_at_start + msg

def send_sms (message, to_phone_no):
    account_sid = os.environ.get('TWILIO_ACCOUNT_SID')
    auth_token = os.environ.get('TWILIO_AUTH_TOKEN')
    client = Client(account_sid, auth_token)

    from_phone_no = '+13018613110'
    message = client.messages \
        .create(
            body=message,
            from_= from_phone_no,
            to=to_phone_no
        )
    print ('message sent, message sid:', message.sid)

if __name__ == "__main__":  # execute this program only if it is run as a script (not by 'import')
    print("This is " + os.path.basename(__file__))
    receiveMessageTask()
