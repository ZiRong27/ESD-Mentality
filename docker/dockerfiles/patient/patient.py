from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from os import environ #For docker use

from datetime import datetime
import json
import pika
import os

app = Flask(__name__)
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_patient'
#app.config['SQLALCHEMY_DATABASE_URI'] = environ.get('dbURL')
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
    #I changed everything to string in sql database as there will be error if you submit a string to a column defined as integer
    data = request.get_json()
    #We use **data to retrieve all the info in the data array, which includes username, password, salutation, name, dob etc
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
    #Checkpassword is only used to check whether the user provided the correct oldpassword. After checking we need to delete it
    #as we do not want it in sql database. The newpassword is already stored in password
    #del data["checkpassword"]
    #We use **data to retrieve all the info in the data array, which includes username, password, salutation, name, dob etc
    #patient = Patient(**data)
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

@app.route("/view-all-patients") 
def get_all():
    return jsonify([patient.json() for patient in Patient.query.all()])


#Function: Search patient by id
@app.route("/patient/<string:patient_id>")
def find_by_patientid(patient_id):
    patient = Patient.query.filter_by(patient_id=patient_id).first()
    if patient:
        return jsonify(patient.json())
    return jsonify({"message": "Patient not found."}), 404

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

# AMQP
# send patient details (phone number, name, patient_id) to appointment microservice
def send_patient_details(patient):

    # default username / password to the borker are both 'guest'
    hostname = "localhost" # default broker hostname. Web management interface default at http://localhost:15672
    port = 5672 # default messaging port.
    # connect to the broker and set up a communication channel in the connection
    connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))
        # Note: various network firewalls, filters, gateways (e.g., SMU VPN on wifi), may hinder the connections;
        # If "pika.exceptions.AMQPConnectionError" happens, may try again after disconnecting the wifi and/or disabling firewalls
    channel = connection.channel()

    # set up the exchange if the exchange doesn't exist
    exchangename="patient_details"
    channel.exchange_declare(exchange=exchangename, exchange_type='topic')

    # prepare the message body content
    message = json.dumps(patient, default=str) # convert a JSON object to a string

    channel.queue_declare(queue='patient', durable=True) # make sure the queue used by Shipping exist and durable
    channel.queue_bind(exchange=exchangename, queue='patient', routing_key='*.details') # make sure the queue is bound to the exchange
    channel.basic_publish(exchange=exchangename, routing_key="patient.details", body=message, properties=pika.BasicProperties(delivery_mode = 2)) # make message persistent within the matching queues until it is received by some receiver (the matching queues have to exist and be durable and bound to the exchange, which are ensured by the previous two api calls)
        
    print("Patient details sent to appointment.")
    # close the connection to the broker
    connection.close()

# execute this program for AMQP - talking to appointment.py
# if __name__ == "__main__":  # execute this program only if it is run as a script (not by 'import')
#     print("This is " + os.path.basename(__file__) + ": sending patient details...")
#     patient = {
#             'patient_id': 20, 
#             'phone' : '+6591131622'
#         }
#     print(patient)
#     send_patient_details(patient)
   


#This is for flask app
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5001, debug=True)
