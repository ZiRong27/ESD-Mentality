
#!/usr/bin/env python3
# The above shebang (#!) operator tells Unix-like environments
# to run this file as a python3 script

import json
import sys
import os
import random
import datetime

# Communication patterns:
# Use a message-broker with 'topic' exchange to enable interaction
import pika
# If see errors like "ModuleNotFoundError: No module named 'pika'", need to
# make sure the 'pip' version used to install 'pika' matches the python version used.

def send_order(message):
    phone_number = '+6591131622'



    """inform Shipping/Monitoring/Error as needed"""
    # default username / password to the borker are both 'guest'
    hostname = "localhost" # default broker hostname. Web management interface default at http://localhost:15672
    port = 5672 # default messaging port.
    # connect to the broker and set up a communication channel in the connection
    connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))
        # Note: various network firewalls, filters, gateways (e.g., SMU VPN on wifi), may hinder the connections;
        # If "pika.exceptions.AMQPConnectionError" happens, may try again after disconnecting the wifi and/or disabling firewalls
    channel = connection.channel()

    # set up the exchange if the exchange doesn't exist
    exchangename="appointment_topic"
    channel.exchange_declare(exchange=exchangename, exchange_type='topic')

    to_send = {"phone": phone_number, "message": message }

    # prepare the message body content
    message = json.dumps(to_send) # convert a JSON object to a string
    print(message)
    # inform Shipping and exit
        # prepare the channel and send a message to Shipping
    channel.queue_declare(queue='notification', durable=True) # make sure the queue used by Shipping exist and durable
    channel.queue_bind(exchange=exchangename, queue='notification', routing_key='*.message') # make sure the queue is bound to the exchange
    channel.basic_publish(exchange=exchangename, routing_key="day1.message", body=message,
        properties=pika.BasicProperties(delivery_mode = 2, # make message persistent within the matching queues until it is received by some receiver (the matching queues have to exist and be durable and bound to the exchange, which are ensured by the previous two api calls)
        )
    )
    # close the connection to the broker
    connection.close()



# Execute this program if it is run as a main script (not by 'import')
if __name__ == "__main__":
    print("This is " + os.path.basename(__file__) + ": creating an order...")
    # message = 'An amount of $95 ' +  "has been successfully charged to your bank account for your appointment on 02/04/2020."
    # send_order(message)


    message = "Dear Mr Chris Poskitt, please be informed that you have an upcoming appointment with Rosa Fernandez on 02/04/2020 at 11:00AM."
    send_order(message)

