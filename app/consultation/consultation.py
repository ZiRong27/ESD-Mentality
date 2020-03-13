#!/usr/bin/env python3
# The above shebang (#!) operator tells Unix-like environments
# to run this file as a python3 script
from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS

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

#Function: Get all Books
@app.route("/consultation")
def get_all():
    #return jsonify({"consultation": [consultation.json() for consultation in Consultation.query.all()]})
    return jsonify([consultation.json() for consultation in Consultation.query.all()])

#Function: Get all consultation by Doctor_ID
@app.route("/consultation-by-doctor/<string:doctor_id>")
def get_all_consultation_by_doctor(doctor_id):
    consultation = Consultation.query.filter_by(doctor_id=doctor_id).all()
    if consultation:
        return jsonify(consultation.json())
    return jsonify({"message": "Error retriving consultation."}), 404
    
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