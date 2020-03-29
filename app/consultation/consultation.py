#!/usr/bin/env python3
# The above shebang (#!) operator tells Unix-like environments
# to run this file as a python3 script
from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from sqlalchemy import func

import json
import sys
import os
import random
import datetime
import pika



app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_consultation'
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://admin:IloveESMandPaul!<3@esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com/esd_consultation'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
 
db = SQLAlchemy(app)
CORS(app)

'''
class Consultation(db.Model):
    __tablename__ = 'consultation'

    consultation_id = db.Column(db.Integer, primary_key=True)
    appointment_id = db.Column(db.String, nullable=False)
    doctor_id = db.Column(db.String, nullable=False)
    patient_id = db.Column(db.String, nullable=False)
    diagnosis = db.Column(db.String, nullable=False)
    prescription = db.Column(db.String, nullable=False)
    notes = db.Column(db.String, nullable=False)

    def __init__(self, consultation_id, appointment_id, doctor_id, patient_id, diagnosis, prescription, notes):
        self.consultation_id = consultation_id
        self.appointment_id = appointment_id
        self.doctor_id = doctor_id
        self.patient_id = patient_id
        self.diagnosis = diagnosis
        self.prescription = prescription
        self.notes = notes

    def json(self):
        return 
        {
            "consultation_id": self.consultation_id, 
            "appointment_id": self.appointment_id, 
            "doctor_id": self.doctor_id, 
            "patient_id": self.patient_id,
            "diagnosis": self.diagnosis, 
            "prescription": self.prescription, 
            "notes": self.notes
        }
'''

class Consultation(db.Model):
    __tablename__ = 'consultation'
    consultation_id = db.Column(db.Integer, primary_key=True)
    appointment_id = db.Column(db.String, nullable=False)
    doctor_id = db.Column(db.String, nullable=False)
    patient_id = db.Column(db.String, nullable=False)
    diagnosis = db.Column(db.String, nullable=False)
    prescription = db.Column(db.String, nullable=False)
    notes = db.Column(db.String, nullable=False)  

    def json(self):
        dto = {
            "consultation_id": self.consultation_id, 
            "appointment_id": self.appointment_id, 
            "doctor_id": self.doctor_id, 
            "patient_id": self.patient_id,
            "diagnosis": self.diagnosis, 
            "prescription": self.prescription, 
            "notes": self.notes
        }
        return dto  

#Function: Get all Consultation
@app.route("/consultation")
def get_all():
    return jsonify([consultation.json() for consultation in Consultation.query.all()])

#Function: Get consultation by consultation Id -> For Doctor & Patient to View
@app.route("/consultation-by-consultationid/<string:consultation_id>")
def find_by_appointment_id(consultation_id):
    consultation = Consultation.query.filter_by(consultation_id=consultation_id).first()
    if consultation:
        return jsonify(consultation.json())
    return jsonify({"message": "consultation not found."}), 404

#Function: Get all consultation by Doctor_ID -> For Doctor to view
@app.route("/consultation-by-doctor/<string:doctor_id>")
def get_all_consultation_by_doctor(doctor_id):
    return jsonify([consultation.json() for consultation in Consultation.query.filter(Consultation.doctor_id.endswith(doctor_id)).all()])
    data = []
    for consultation in Consultation.query.filter(Consultation.patient_id.endswith(patient_id)).all():
        data.append(consultation.json())
    if data:
        return jsonify(data)
    return jsonify({"message": "consultation by doctor id not found."}), 404

#Function: Get all consultation by Patient_id -> For Patient to view
@app.route("/consultation-by-patient/<string:patient_id>")
def get_all_consultation_by_patient(patient_id):
    #return jsonify([consultation.json() for consultation in Consultation.query.filter(Consultation.patient_id.endswith(patient_id)).all()])
    data = []
    for consultation in Consultation.query.filter(Consultation.patient_id.endswith(patient_id)).all():
        data.append(consultation.json())
    if data:
        return jsonify(data)
    return jsonify({"message": "consultation by patient id not found."}), 404

#Function: Create consultation
@app.route("/convert-to-consultation", methods=['POST'])
def register():
    data = request.get_json()
    #Checks if there exists another patient with the same username
    if (Consultation.query.filter_by(consultation_id=data["consultation_id"]).first()):
        return jsonify({"message": "Consultation ID already exist, please contact support team"}), 400
    #I changed everything to string in sql database as there will be error if you submit a string to a column defined as integer
    data = request.get_json()
    #We use **data to retrieve all the info in the data array, which includes username, password, salutation, name, dob etc
    consultation = Consultation(**data)
    try:
        db.session.add(consultation)
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred creating the Consultation."}), 500
    return jsonify(consultation.json()), 201

    # Flask app
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5004, debug=True)