from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from os import environ #For docker use

import json
import pika
import os

app = Flask(__name__)
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_patient'
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://admin:IloveESMandPaul!<3@esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com/esd_patient'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
 
db = SQLAlchemy(app)
CORS(app)

class Patient(db.Model):
    __tablename__ = 'patient'
    patient_id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String, nullable=False)
    gender = db.Column(db.String, nullable=False)
    dob = db.Column(db.String, nullable=False)
    phone = db.Column(db.String, nullable=False)
    salutation  = db.Column(db.String, nullable=False)
    username = db.Column(db.String, nullable=False)
    password = db.Column(db.String, nullable=False)   

    def json(self):
        dto = {
            'patient_id': self.patient_id, 
            'name': self.name,
            'gender' : self.gender ,
            'dob' : self.dob ,
            'phone' : self.phone ,
            'salutation' : self.salutation ,
            'username' : self.username ,
            'password' : self.password 
        }
        return dto  

#Note that post does not have anything in the url. Its all in the body of the request
#@app.route("/login-process/<string:username>&<string:password>", methods=['POST'])
@app.route("/login-process", methods=['POST'])
def login():
    data = request.get_json()
    patient = Patient.query.filter_by(username=data["username"]).filter_by(password=data["password"]).first()
    if patient:
        return jsonify(patient.json())
    return jsonify({'message': 'Wrong username/password! '}), 404   

@app.route("/register-process", methods=['POST'])
def register():
    data = request.get_json()
    #Checks if there exists another patient with the same username
    if (Patient.query.filter_by(username=data["username"]).first()):
        return jsonify({"message": "A patient with username '{}' already exists.".format(data['username'])}), 400

    data = request.get_json()
    patient = Patient(**data)

    try:
        db.session.add(patient)
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred creating the patient."}), 500
 
    return jsonify(patient.json()), 201

@app.route("/update-profile-process", methods=['POST'])
#Retrieves a specific patient details
def getPatient():
    data = request.get_json()
    patient = Patient.query.filter_by(username=data["username"]).first()
    if patient:
        return jsonify(patient.json())
    return jsonify({'message': 'Wrong username/password! '}), 404   

@app.route("/update-profile-update", methods=['POST'])
#Updates a specific patient details
def updatePatient():
    #I changed everything to string in sql database as there will be error if you submit a string to a column defined as integer
    data = request.get_json()
    #Checks if the provided password is correct
    patient = Patient.query.filter_by(username=data["username"]).first()
    patientdata = patient.json()
    if (data["checkpassword"] != patientdata["password"]):
        return jsonify({"message": "The provided password is wrong."}), 400
    # Checkpassword is only used to check whether the user provided the correct oldpassword. After checking we need to delete it
    # as we do not want it in sql database. The newpassword is already stored in password
    # del data["checkpassword"]
    try:
        setattr(patient, 'name', data["name"])
        setattr(patient, 'gender', data["gender"])
        setattr(patient, 'dob', data["dob"])
        setattr(patient, 'phone', data["phone"])
        setattr(patient, 'salutation', data["salutation"])
        setattr(patient, 'password', data["password"])
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred updating details of the patient."}), 500
 
    return jsonify(patient.json()), 201

# Function: return all patient, without the unnecessary data -> username, password, dob, 
@app.route("/view-all-patients") 
def get_all():
    data = []
    for patient in Patient.query.with_entities(Patient.patient_id, Patient.name, Patient.phone, Patient.salutation, Patient.gender, Patient.dob).all():
        ele = {}
        ele.update( {'patient_id' : patient[0]} )
        inputName = patient[1]
        patientName = splitName( inputName )
        ele.update( {'name' : patientName} )
        ele.update( {'phone' : patient[2]} )
        ele.update( {'salutation' : patient[3]} )
        ele.update( {'gender' : patient[4]} )
        ele.update( {'dob' : patient[5]} )
        data.append(ele)
    if data:
        return jsonify(data)
    return jsonify({"message": "Error retriving all patients."}), 404


@app.route("/patient/<string:patient_id>")
def find_by_patientid(patient_id):
    data = []
    patient = Patient.query.filter_by(patient_id=patient_id).first()
    data.append(patient.json())
    for ele in data:
        result = {}
        result.update( {'patient_id' : ele["patient_id"]} )
        inputName = ele["name"]
        patientName = splitName( inputName )
        result.update( {'name' : patientName} )
        result.update( {'phone' : ele["phone"]} )
        result.update( {'salutation' : ele["salutation"]} )
        result.update( {'gender' : ele["gender"]} )
        result.update( {'dob' : ele["dob"]} )
        print(result)
    if result:
        return jsonify(result)
    return jsonify({"message": "Patient not found."}), 404

def splitName(inputName):
    print(inputName)
    textList = inputName.split(",")
    newTextList = []
    newTextList.append(textList[-1])
    newTextList.append(textList[0])
    newText = ' '.join(newTextList)
    return newText

# --------------------------------- #
# <!-- Patient Allergies Database -->
class Allergies(db.Model):
    __tablename__ = 'patient_allergies'
    patient_id = db.Column(db.Integer, primary_key=True)
    allergies = db.Column(db.String, primary_key=True)

    def json(self):
        dto = {
            'patient_id': self.patient_id, 
            'allergies': self.allergies,
        }
        return dto

@app.route("/allergies/<string:patient_id>")
def allergies(patient_id):
    allergies = Allergies.query.filter_by(patient_id=patient_id).first()
    if allergies:
        return jsonify(allergies.json())
    return jsonify({"message": "allergies data missing error."}), 404


# --------------------------------- #
# <!-- Patient Medical History -->
class History(db.Model):
    __tablename__ = 'patient_medical_history'
    patient_id = db.Column(db.Integer, primary_key=True)
    medical_history = db.Column(db.String, primary_key=True)

    def json(self):
        dto = {
            'patient_id': self.patient_id, 
            'medical_history': self.medical_history,
        }
        return dto

@app.route("/history/<string:patient_id>")
def history(patient_id):
    history = History.query.filter_by(patient_id=patient_id).first()
    if history:
        return jsonify(history.json())
    return jsonify({"message": "patient history data missing."}), 404

#This is for flask app
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5001, debug=True)
