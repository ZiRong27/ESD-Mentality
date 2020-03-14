
#!/usr/bin/env python3
# The above shebang (#!) operator tells Unix-like environments
# to run this file as a python3 script
from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from os import environ #For docker use

import json
import sys
import os
import random
import datetime
import pika


app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_appointment'
# #app.config['SQLALCHEMY_DATABASE_URI'] = environ.get('dbURL')
# app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://admin:IloveESMandPaul!<3@esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com/esd_appointment'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
 
db = SQLAlchemy(app)
CORS(app)

hostname = "localhost" # default hostname
port = 5672 # default port
# connect to the broker and set up a communication channel in the connection
connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))
    # Note: various network firewalls, filters, gateways (e.g., SMU VPN on wifi), may hinder the connections;
    # If "pika.exceptions.AMQPConnectionError" happens, may try again after disconnecting the wifi and/or disabling firewalls
channel = connection.channel()
# set up the exchange if the exchange doesn't exist
exchangename="patient_details"
channel.exchange_declare(exchange=exchangename, exchange_type='topic')

class Appointment(db.Model):
    __tablename__ = 'appointment'
 
    appointment_id = db.Column(db.Integer, primary_key=True)
    doctor_id = db.Column(db.String,nullable=False)
    patient_id = db.Column(db.String, nullable=False)
    date = db.Column(db.String, nullable=False)
    time = db.Column(db.String, nullable=False)
    
    
 
    # def __init__(self, appointment_id, doctor_id, patient_id, date, time):
    #     self.appointment_id = appointment_id
    #     self.doctor_id = doctor_id
    #     self.patient_id = patient_id
    #     self.date = date
    #     self.time = time
     
 
    
    # return an appointment item as a JSON object
    def json(self):
        return {'appointment_id': self.appointment_id, 
                'doctor_id': self.doctor_id, 
                'patient_id': self.patient_id, 
                'date': self.date, 
                'time': self.time, 
                }


@app.route("/appointment/<string:doctor_id>")
def find_by_doctor_id(doctor_id):
    appointment = Appointment.query.filter_by(doctor_id=doctor_id).first()
    print(appointment)
    if appointment:
        return jsonify(appointment.json())
    return jsonify({"message": "Appointment not found."}), 404

# note -> guys i have to change the name of the route here cus, appointment is used by doctor id
@app.route("/appointment-by-id/<string:appointment_id>")
def find_by_appointment_id(appointment_id):
    appointment = Appointment.query.filter_by(appointment_id=appointment_id).first()
    if appointment:
        return jsonify(appointment.json())
    return jsonify({"message": "Appointment base on appointment is not found."}), 404

@app.route("/appointment-by-date/<string:date>")
def find_by_date(date):
    #appointment = Appointment.query.filter_by(date=date)
    #print(appointment)
    return jsonify([appointment.json() for appointment in Appointment.query.filter_by(date=date)])
    # if appointment:
    #     return jsonify(appointment.json() for appt in appointment)
    # return jsonify({"message": "Appointment not found."}), 404


@app.route("/create-appointment", methods=['POST'])
def create_appointment():
    data = request.get_json()
    #Checks if a timeslot is booked already
    if (Appointment.query.filter_by(doctor_id=data["doctor_id"],date=data["date"],time=data["time"]).first()):
        return jsonify({"message": "The timeslot already exists."}), 400
    #We use **data to retrieve all the info in the data array, which includes username, password, salutation, name, dob etc
    appointment = Appointment(**data)
    try:
        db.session.add(appointment)
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred creating the appointment."}), 500
 
    return jsonify(appointment.json()), 201
   

# @app.route("/update-appointment", methods=['POST'])
# #Updates a specific appointment details
# def updateAppointment():
#     #I changed everything to string in sql database as there will be error if you submit a string to a column defined as integer
#     data = request.get_json()
#     appointment = Appointment(**data)
#     try:
#         setattr(appointment, 'date', data["date"])
#         setattr(appointment, 'time', data["time"])
#         db.session.commit()
#     except:
#         return jsonify({"message": "An error occurred updating details of the appointment."}), 500
 
#     return jsonify(patient.json()), 201


# need delete appointment?


@app.route("/view-all-appointments") 
def get_all():
    return jsonify([appointment.json() for appointment in Appointment.query.all()])


# AMQP
# get phone number from patient microservice
def receive_patient_details():
    # prepare a queue for receiving messages
    channelqueue = channel.queue_declare(queue="patient", durable=True) # 'durable' makes the queue survive broker restarts so that the messages in it survive broker restarts too
    queue_name = channelqueue.method.queue
    channel.queue_bind(exchange=exchangename, queue=queue_name, routing_key='*.details') # bind the queue to the exchange via the key
        # any routing_key with two words and ending with '.message' will be matched
    
    # set up a consumer and start to wait for coming messages
    channel.basic_qos(prefetch_count=1) # The "Quality of Service" setting makes the broker distribute only one message to a consumer if the consumer is available (i.e., having finished processing and acknowledged all previous messages that it receives)
    channel.basic_consume(queue=queue_name, on_message_callback=callback, auto_ack=True) # 'auto_ack=True' acknowledges the reception of a message to the broker automatically, so that the broker can assume the message is received and processed and remove it from the queue
    channel.start_consuming() # an implicit loop waiting to receive messages; it doesn't exit by default. Use Ctrl+C in the command window to terminate it.

def callback(channel, method, properties, body): # required signature for the callback; no return
    print("Received patient details by patient microservice")
    result = process_patient_details(json.loads(body)) # json expected {phone_no, message}
    # print processing result; not really needed
    json.dump(result, sys.stdout, default=str) # convert the JSON object to a string and print out on screen
    print() # print a new line feed to the previous json dump
    print() # print another new line as a separator

def process_patient_details(details):
    #send_appointment(appointment)
    print("Processing patient details:")
    return details
    
    
# @app.route("/send-appointment") 
# AMQP: to send to notification microservice
def send_appointment(appointment):
    phone = appointment['phone']
    patient_name = appointment['patient_name']
    doctor_name = appointment['doctor_name']
    time = appointment['date']
    date = appointment['time']
    
    hostname = "localhost" # default broker hostname. 
    port = 5672 # default messaging port.
    # connect to the broker and set up a communication channel in the connection
    connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))
    channel = connection.channel()
    # set up the exchange if the exchange doesn't exist
    exchangename="appointment_topic"
    channel.exchange_declare(exchange=exchangename, exchange_type='topic')

    # prepare the message body content
    #message = json.dumps(appointment, default=str) # convert a JSON object to a string
    for_patient_message = "Hi " + patient_name + "! This is a reminder that you have an upcoming appointment with Dr " + doctor_name + " at " + time + " tomorrow, " + date + "."

    result = {"phone": phone, "message": for_patient_message}
    message = json.dumps(result, default=str)
    print(message)
    # inform Notification and exit
    # prepare the channel and send a message to Notification
    channel.queue_declare(queue='notification', durable=True) # make sure the queue used by Notification exist and durable
    channel.queue_bind(exchange=exchangename, queue='notification', routing_key='*.message') # make sure the queue is bound to the exchange
    channel.basic_publish(exchange=exchangename, routing_key="notification.message", body=message,
        properties=pika.BasicProperties(delivery_mode = 2))# make message persistent within the matching queues until it is received by some receiver (the matching queues have to exist and be durable and bound to the exchange, which are ensured by the previous two api calls)
   
    
    # close the connection to the broker
    connection.close()


# execute this program for AMQP - talking to notification.py
# if __name__ == "__main__":  # execute this program only if it is run as a script (not by 'import')
#     print("This is " + os.path.basename(__file__))
#     appointment = {'appointment_id': 20, 
#                 'doctor_id': '1', 
#                 'doctor_name': 'Ong',
#                 'patient_id': '20', 
#                 'date': '2020-04-01', 
#                 'time': '10:30:00', 
#                 'phone' : '+6591131622',
#                 'patient_name': 'Mandy'
#                 }
                 
#     send_appointment(appointment)




# execute this program for AMQP - talking to patient.py
# if __name__ == "__main__":  
#     print("This is " + os.path.basename(__file__) + ": receiving patient details...")
#     receive_patient_details()
    

# # Flask app
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5003, debug=True)