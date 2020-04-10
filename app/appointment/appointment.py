
#!/usr/bin/env python3
# The above shebang (#!) operator tells Unix-like environments
# to run this file as a python3 script
from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from os import environ #For docker use
from datetime import datetime, timedelta
from pytz import timezone

import json
import sys
import os
import pika, os


app = Flask(__name__)
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root:root@localhost:8889/esd_appointment'
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_appointment'
#app.config['SQLALCHEMY_DATABASE_URI'] = environ.get('dbURL')
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://admin:IloveESMandPaul!<3@esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com/esd_appointment'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
 
db = SQLAlchemy(app)
CORS(app)

# Access the CLODUAMQP_URL environment variable and parse it (fallback to localhost)
url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
params = pika.URLParameters(url)
connection = pika.BlockingConnection(params)
#Set up rabbitmq for payment to send a message to notification.py upon successful payment
hostname = "localhost" # default hostname
port = 5672 # default port
# connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))
channel = connection.channel()
# set up the exchange if the exchange doesn't exist
exchangename="mentality"
channel.exchange_declare(exchange=exchangename, exchange_type='topic')



class Appointment(db.Model):
    __tablename__ = 'appointment'
 
    appointment_id = db.Column(db.Integer, primary_key=True, autoincrement=True)
    doctor_id = db.Column(db.String,nullable=False)
    patient_id = db.Column(db.Integer, nullable=False)
    date = db.Column(db.String, nullable=False)
    time = db.Column(db.String, nullable=False)
    payment_id = db.Column(db.Integer, nullable=False)
    
    
 
    def __init__(self, doctor_id, patient_id, date, time, payment_id):
        self.doctor_id = doctor_id
        self.patient_id = patient_id
        self.date = date
        self.time = time
        self.payment_id = payment_id
     
 
    
    # return an appointment item as a JSON object
    def json(self):
        return {'appointment_id': self.appointment_id, 
                'doctor_id': self.doctor_id, 
                'patient_id': self.patient_id, 
                'date': self.date, 
                'time': self.time, 
                'payment_id':self.payment_id
                }

    def print_q(self):
        print ("pid", self.patient_id, "date", self.date, "did", self.doctor_id, "time", self.time, "paymentid", self.payment_id)


def send_appointment_reminder(message, patient_id, appointment_date, appointment_time):

    # set delay for messaget
    # reminder to be sent at 6pm the day before
    datetime_object = datetime.strptime(appointment_date, '%Y-%m-%d')
    datetime_object = datetime_object - timedelta(days=1)
    datetime_object = datetime_object.replace(hour=18)

    singapore = timezone('Asia/Singapore')
    now = datetime.now(singapore)
    now = now.replace(tzinfo=None)

    # set delay time in milliseconds
    diff = datetime_object - now
    delay_duration = diff.total_seconds() * 1000

    if delay_duration < 0:
        delay_duration = 5

    
    channel = connection.channel()
    routing_key = "appointment.message"
    message = {"message": message, "patient_id": patient_id}
    message=json.dumps(message, default=str)
    # channel.basic_publish(exchange=exchangename, routing_key="appointment.message", body=message,
    # properties=pika.BasicProperties(delivery_mode = 2))# make message persistent within the matching queues until it is received by some receiver (the matching queues have to exist and be durable and bound to the exchange, which are ensured by the previous two api calls)

    print (delay_duration)
    hold_queue = "delay.{0}.{1}.{2}".format(
        delay_duration, exchangename, routing_key)
    hold_queue_arguments = {
        # Exchange where to send messages after TTL expiration.
        "x-dead-letter-exchange": exchangename,
        # Routing key which use when resending expired messages.
        "x-dead-letter-routing-key": routing_key,
        # Time in milliseconds
        # after that message will expire and be sent to destination.
        "x-message-ttl": delay_duration
        # # Time after that the queue will be deleted.
        # "x-expires": delay_duration * 2
    }

    # It's necessary to redeclare the queue each time
    #  (to zero its TTL timer).
    channel.queue_declare(queue=hold_queue,
                          durable=True,
                          exclusive=False,
                          arguments=hold_queue_arguments)
    print ("reach here")
    channel.basic_publish(
        exchange= '',  # Publish to the default exchange.
        routing_key=hold_queue, body=message,
        # Make the message persistent.
        properties=pika.BasicProperties(delivery_mode=2,)
    )
    print (message)
    # The channel is expendable.
    channel.close()


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

@app.route("/appointment-by-date/<string:date>/<string:doctor_id>")
def find_by_date_and_doctorid(date,doctor_id):
    return jsonify([appointment.json() for appointment in Appointment.query.filter_by(date=date,doctor_id=doctor_id)])



@app.route("/create-appointment", methods=['POST'])
def create_appointment():
    data = request.get_json()
    print (data)
    appointment = Appointment(**data)
    print (data["date"])

    # #Checks if a timeslot is booked already by another user
    # if (Appointment.query.filter_by(doctor_id=data["doctor_id"],date=data["date"],time=data["time"]).first()):
    #     return jsonify({"message": "The timeslot is already booked by another user."}), 400
    # #Ensures that duplicate appointment is not created given a payment id
    # elif (Appointment.query.filter_by(doctor_id=data["doctor_id"],date=data["date"],time=data["time"], payment_id=data["payment_id"]).first()):
    #     return jsonify(appointment.json()), 201
    
    try:
        # db.session.add(appointment)
        # db.session.commit()
        message = "Please be reminded that you have an upcoming appointment on " + data["date"] + " at " + data["time"] + "."
        send_appointment_reminder(message, data["patient_id"], data["date"], data["time"])
        return jsonify(appointment.json()), 201
    except:
        return jsonify({"message": "An error occurred creating the appointment."}), 500
 

    

#FUNCTION: Delete by Appointment
'''
@app.route("/delete-appointment/<string:appointment_id>", methods=['POST'])
def delete_appointment(appointment_id):
    data = Appointment.query.filter_by(appointment_id=appointment_id).first()
    try:
        db.session.delete(data)
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred while deleting the appointment."}), 500
 
    return jsonify(appointment.json()), 201
'''

@app.route("/delete-appointment", methods=['POST'])
def delete_appointment():
    data = request.get_json()
    appointment = Appointment.query.filter_by(appointment_id=data["appointment_id"]).first()
    try:
        db.session.delete(appointment)
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred while deleting the appointment."}), 500
    return jsonify(history.json()), 201



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

# capture appointment history.
class History(db.Model):
    __tablename__ = 'history'
    appointment_id = db.Column(db.Integer, primary_key=True)
    doctor_id = db.Column(db.String,nullable=False)
    patient_id = db.Column(db.Integer, nullable=False)
    date = db.Column(db.String, nullable=False)
    time = db.Column(db.String, nullable=False)
    payment_id = db.Column(db.Integer, nullable=False)

    def json(self):
        dto = {
            "appointment_id": self.appointment_id, 
            "doctor_id": self.doctor_id, 
            "patient_id": self.patient_id, 
            "date": self.date,
            "time": self.time, 
            "payment_id": self.payment_id
        }
        return dto  
''' 
class History(db.Model):
    __tablename__ = 'history'
 
    appointment_id = db.Column(db.Integer, primary_key=True)
    doctor_id = db.Column(db.String,nullable=False)
    patient_id = db.Column(db.String, nullable=False)
    date = db.Column(db.String, nullable=False)
    time = db.Column(db.String, nullable=False)
    payment_id = db.Column(db.Integer, nullable=False)
    
    def __init__(self, doctor_id, patient_id, date, time, payment_id):
        self.doctor_id = doctor_id
        self.patient_id = patient_id
        self.date = date
        self.time = time
        self.payment_id = payment_id
     
    # return an appointment item as a JSON object
    def json(self):
        return {'appointment_id': self.appointment_id, 
                'doctor_id': self.doctor_id, 
                'patient_id': self.patient_id, 
                'date': self.date, 
                'time': self.time, 
                'payment_id':self.payment_id
                }

    #def print_q(self):
        #print ("pid", self.patient_id, "date", self.date, "did", self.doctor_id, "time", self.time, "paymentid", self.payment_id)
'''

# note -> guys i have to change the name of the route here cus, appointment is used by doctor id
@app.route("/get-appointment-id-history/<string:appointment_id>")
def find_history_by_appointment_id(appointment_id):
    history = History.query.filter_by(appointment_id=appointment_id).first()
    if history:
        return jsonify(history.json())
    return jsonify({"message": "This Appointment is not found."}), 404

#Function: Get all appointment history
@app.route("/get-all-appointment-history/<string:patient_id>") 
def get_all_history_appointment_by_patient(patient_id):
    #return jsonify([consultation.json() for consultation in Consultation.query.filter(Consultation.patient_id.endswith(patient_id)).all()])
    data = []
    for history in History.query.filter(History.patient_id.endswith(patient_id)).all():
        data.append(history.json())
    if data:
        return jsonify(data)
    return jsonify({"message": "history appointment by patient id not found."}), 404

#Function: create appointment history
'''
@app.route("/appointment-history/<string:appointment_id>",methods=['POST'])
def add_appointment_history(appointment_id):
    history = History.query.filter_by(appointment_id=appointment_id).first()
    if history:
        return jsonify({"message": "appointment exisit in the history, please check with admin"}), 400
    data = request.get_json()
    history = History(appointment_id, **data)
    try:
        db.session.add(history)
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred creating appointment history"}), 500
    return jsonify(history.json()), 201
'''

@app.route("/appointment-history", methods=['POST'])
def add_appointment_history():
    data = request.get_json()
    #Checks if there exists another patient with the same username
    if (History.query.filter_by(appointment_id=data["appointment_id"]).first()):
        return jsonify({"message": "This Appointment ID already exist, please contact support team"}), 400
    #We use **data to retrieve all the info in the data array, which includes username, password, salutation, name, dob etc
    data = request.get_json()
    history = History(**data)
    try:
        db.session.add(history)
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred creating the Consultation."}), 500
    return jsonify(history.json()), 201



# Flask app
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5003, debug=True)

    

