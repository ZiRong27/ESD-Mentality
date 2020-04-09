#!/usr/bin/env python3

import sys
import os
import csv

import pika
import json

# External API used
from twilio.rest import Client

def receivePatientPhone():
    # channel settings
    url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
    params = pika.URLParameters(url)
    connection = pika.BlockingConnection(params)
    channel = connection.channel()

    # set up the exchange if the exchange doesn't exist
    exchangename="patient_details"
    channel.exchange_declare(exchange=exchangename, exchange_type='topic')
    channel.queue_declare(queue='patient', durable=True) # make sure the queue used by Shipping exist and durable
    channel.queue_bind(exchange=exchangename, queue='patient', routing_key='notification.reply.phone_number') # make sure the queue is bound to the exchange
    
    # set up a consumer and start to wait for coming messages
    channel.basic_qos(prefetch_count=1) # The "Quality of Service" setting makes the broker distribute only one message to a consumer if the consumer is available (i.e., having finished processing and acknowledged all previous messages that it receives)
    channel.basic_consume(queue='patient',
            on_message_callback=reply_callback, # set up the function called by the broker to process a received message
    ) # prepare the reply_to receiver
    channel.start_consuming() # an implicit loop waiting to receive messages; it doesn't exit by default. Use Ctrl+C in the command window to terminate it.

def reply_callback(channel, method, properties, body): # required signature for a callback; no return
    """processing function called by the broker when a message is received"""
    # Load correlations for existing created orders from a file.
    # - In practice, using DB (as part of the order DB) is a better choice than using a file.
    rows = []
    with open("corrids.csv", 'r', newline='') as corrid_file: # 'with' statement in python auto-closes the file when the block of code finishes, even if some exception happens in the middle
        csvreader = csv.DictReader(corrid_file)
        for row in csvreader:
            rows.append(row)
    # Check if the reply message contains a valid correlation id recorded in the file.
    # - Assume each line in the file is in this CSV format: <order_id>, <correlation_id>, <status>, ...
    matched = False
    for row in rows:
        if not 'correlation_id' in row:
            print('Warning for corrids.csv: no "correlation_id" for an order:', row)
            continue
        corrid = row['correlation_id']
        if corrid == properties.correlation_id: # check if the reply message matches one request message based on the correlation id
            print("--Matched reply message with a correlation ID: " + corrid)
            # Can do anything needed for the scenario here, e.g., may update the 'status', or inform UI or other applications/services.
            result = json.loads(body) 
            phone_no = result["phone"]
            phone_no = "+65" + phone_no
            # delete the data from database
            send_sms(row["message"], phone_no)
            matched = True
            break
    if not matched:
        print("--Wrong reply correlation ID: No match of " + properties.correlation_id)
        print()

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
if __name__ == "__main__":
    print("This is " + os.path.basename(__file__) + ": listening for a reply from shipping for an order...")
    receivePatientPhone()
