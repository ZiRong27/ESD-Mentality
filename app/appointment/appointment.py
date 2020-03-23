
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


app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root:root@localhost:8889/esd_appointment'
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
 
    appointment_id = db.Column(db.Integer, primary_key=True, autoincrement=True)
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

    def print_q(self):
        print ("pid", self.patient_id, "date", self.date, "did", self.doctor_id, "time", self.time, "paymentid", self.payment_id)


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
def create_appointment_http():
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


# Flask app
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5003, debug=True)

    

