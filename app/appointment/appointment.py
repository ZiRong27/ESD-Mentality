
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
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_appointment'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
 
db = SQLAlchemy(app)
CORS(app)

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
    if appointment:
        return jsonify(appointment.json())
    return jsonify({"message": "Appointment not found."}), 404


@app.route("/appointment/<string:appointment_id>")
def find_by_appointment_id(appointment_id):
    appointment = Appointment.query.filter_by(appointment_id=appointment_id).first()
    if appointment:
        return jsonify(appointment.json())
    return jsonify({"message": "Appointment not found."}), 404


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
   

@app.route("/update-appointment", methods=['POST'])
#Updates a specific appointment details
def updateAppointment():
    #I changed everything to string in sql database as there will be error if you submit a string to a column defined as integer
    data = request.get_json()
    try:
        setattr(patient, 'date', data["date"])
        setattr(patient, 'time', data["time"])
        db.session.commit()
    except:
        return jsonify({"message": "An error occurred updating details of the appointment."}), 500
 
    return jsonify(patient.json()), 201


# need delete appointment?


@app.route("/view-all-appointments") 
def get_all():
    return jsonify([appointment.json() for appointment in Appointment.query.all()])

# get phone number from patient microservice
def getPhoneNumber():
    # prepare a queue for receiving messages
    channelqueue = channel.queue_declare(queue="appointment", durable=True) # 'durable' makes the queue survive broker restarts so that the messages in it survive broker restarts too
    queue_name = channelqueue.method.queue
    channel.queue_bind(exchange=exchangename, queue=queue_name, routing_key='*.message') # bind the queue to the exchange via the key
        # any routing_key with two words and ending with '.message' will be matched
    
    # set up a consumer and start to wait for coming messages
    channel.basic_qos(prefetch_count=1) # The "Quality of Service" setting makes the broker distribute only one message to a consumer if the consumer is available (i.e., having finished processing and acknowledged all previous messages that it receives)
    channel.basic_consume(queue=queue_name, on_message_callback=callback, auto_ack=True) # 'auto_ack=True' acknowledges the reception of a message to the broker automatically, so that the broker can assume the message is received and processed and remove it from the queue
    channel.start_consuming() # an implicit loop waiting to receive messages; it doesn't exit by default. Use Ctrl+C in the command window to terminate it.

def callback(channel, method, properties, body): # required signature for the callback; no return
    result = processPhoneNumber(json.loads(body)) # json expected {phone_no, message}

def processPhoneNumber():
    send_appointment(appointment)
    
    
@app.route("/send-appointment") 
# AMQP: to send to notification microservice
def send_appointment(appointment):
    hostname = "localhost" # default broker hostname. 
    port = 5672 # default messaging port.
    # connect to the broker and set up a communication channel in the connection
    connection = pika.BlockingConnection(pika.ConnectionParameters(host=hostname, port=port))
    channel = connection.channel()
    # set up the exchange if the exchange doesn't exist
    exchangename="appointment_topic"
    channel.exchange_declare(exchange=exchangename, exchange_type='topic')

    # prepare the message body content
    message = json.dumps(appointment, default=str) # convert a JSON object to a string


    # inform Notification and exit
    # prepare the channel and send a message to Notification
    channel.queue_declare(queue='notification', durable=True) # make sure the queue used by Notification exist and durable
    channel.queue_bind(exchange=exchangename, queue='notification', routing_key='*.message') # make sure the queue is bound to the exchange
    channel.basic_publish(exchange=exchangename, routing_key="day1.message", body=message,
        properties=pika.BasicProperties(delivery_mode = 2, # make message persistent within the matching queues until it is received by some receiver (the matching queues have to exist and be durable and bound to the exchange, which are ensured by the previous two api calls)
        )
    )
    # close the connection to the broker
    connection.close()



# if __name__ == "__main__":  # execute this program only if it is run as a script (not by 'import')
#     print("This is " + os.path.basename(__file__))
#     send_appointment(appointment)

# Flask app
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5003, debug=True)