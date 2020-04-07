from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from os import environ #For docker use

from datetime import datetime
import json
import pika

app = Flask(__name__)
# app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_doctor'
#app.config['SQLALCHEMY_DATABASE_URI'] = environ.get('dbURL')
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://admin:IloveESMandPaul!<3@esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com/esd_doctor'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
 
db = SQLAlchemy(app)
CORS(app)

class Doctor(db.Model):
    __tablename__ = 'doctor'
    doctor_id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String, nullable=False)
    gender = db.Column(db.String, nullable=False)
    dob = db.Column(db.String, nullable=False)
    experience = db.Column(db.String, nullable=False)
    specialisation  = db.Column(db.String, nullable=False)
    price = db.Column(db.String, nullable=False)
    username = db.Column(db.String, nullable=False)
    password = db.Column(db.String, nullable=False)   
      

    def json(self):
        dto = {
            'doctor_id': self.doctor_id, 
            'name': self.name,
            'gender' : self.gender ,
            'dob' : self.dob ,
            'experience' : self.experience ,
            'specialisation' : self.specialisation ,
            'price': self.price ,
            'username' : self.username ,
            'password' : self.password
        }
        return dto  

    def json_no_password(self):
        dto = {
            'doctor_id': self.doctor_id, 
            'name': self.name,
            'gender' : self.gender ,
            'dob' : self.dob ,
            'experience' : self.experience ,
            'specialisation' : self.specialisation ,
            'price': self.price,
            'username' : self.username
        }
        return dto  

#Note that post does not have anything in the url. Its all in the body of the request
#@app.route("/login-process/<string:username>&<string:password>", methods=['POST'])
@app.route("/login-process-doctor", methods=['POST'])
def login():
    data = request.get_json()
    doctor = Doctor.query.filter_by(username=data["username"]).filter_by(password=data["password"]).first()
    if doctor:
        return jsonify(doctor.json())
    return jsonify({'message': 'Wrong username/password! '}), 404   

@app.route("/register-process-doctor", methods=['POST'])
def register():
    data = request.get_json()
    #Checks if there exists another patient with the same username
    if (Doctor.query.filter_by(username=data["username"]).first()):
        return jsonify({"message": "A doctor with username '{}' already exists.".format(data['username'])}), 400
    #I changed everything to string in sql database as there will be error if you submit a string to a column defined as integer
    data = request.get_json()
    #We use **data to retrieve all the info in the data array, which includes username, password, salutation, name, dob etc
    doctor = Doctor(**data)
    try:
        db.session.add(doctor)
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred creating the doctor."}), 500
 
    return jsonify(doctor.json()), 201

@app.route("/view-specific-doctor/<string:username>") 
def get_specific_doctor(username):
    doctor = Doctor.query.filter_by(username=username).first()
    if doctor:
        return jsonify(doctor.json_no_password())
    return jsonify({"message": "Doctor not found."}), 404

@app.route("/price/<string:username>") 
def get_price(username):
    price = Doctor.query.filter_by(username=username).first()
    if price:
        return jsonify(price.json()['price'])
    return jsonify({"message": "Price not found."}), 404

@app.route("/view-all-doctors") 
def get_all():
    return jsonify([doctor.json_no_password() for doctor in Doctor.query.all()])


#Function: Get consultation by consultation Id -> For Doctor & Patient to View
@app.route("/view-specific-doctor-by-id/<string:doctor_id>")
def find_by_doctor_id(doctor_id):
    doctor = Doctor.query.filter_by(doctor_id=doctor_id).first()
    if doctor:
        return jsonify(doctor.json_no_password())
    return jsonify({"message": "Doctor not found."}), 404

#THis is for flask ap
#NOTE THAT ALL MICROSERVICES must use different ports if you want to run them simultaneously!
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5002, debug=True)
