from flask import Flask, render_template, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy.exc import SQLAlchemyError
from flask_cors import CORS

# pip install --upgrade stripe
import stripe

import time
import json
import datetime
import pytz
import requests
from requests.exceptions import HTTPError

import pika

# Access the CLODUAMQP_URL environment variable and parse it (fallback to localhost)
url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
params = pika.URLParameters(url)
connection = pika.BlockingConnection(params)
channel = connection.channel()

# set up the exchange if the exchange doesn't exist
exchangename="mentality"
channel.exchange_declare(exchange=exchangename, exchange_type='topic')


#ip address
appointment_ip = "54.255.201.27:5003"

app = Flask(__name__)
#app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root@localhost:3306/esd_payment'
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://admin:IloveESMandPaul!<3@esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com/esd_payment'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
 
db = SQLAlchemy(app)
CORS(app)


class Payment(db.Model):
    __tablename__ = 'payment'
    patient_id = db.Column(db.String, nullable=False)
    singapore = pytz.timezone('Asia/Singapore')
    date = db.Column(db.Date, nullable = False, default=datetime.datetime.now(singapore))
    payment_id = db.Column(db.Integer, primary_key=True, autoincrement=True)
    amount = db.Column(db.Float, nullable=False)

    def __init__(self, patient_id, amount):
        # sets the properties (of itself when created)
        self.patient_id = patient_id
        self.amount = amount
  

    def json(self):
        dto = {
            'patient_id': self.patient_id, 
            'date' : self.date ,
            'payment_id' : self.payment_id ,
            'amount' : self.amount
        }
        return dto  
    
    def print_q(self):
        # for debugging
        print ("pateitn", self.patient_id, "date", self.date)

### generate access key
pub_key = 'pub_key'
stripe.api_key = 'api_key'

@app.route('/transactionhistory-by-id/<string:patient_id>')
def get_all_payment_by_patientid(patient_id):
    payment = Payment.query.filter_by(patient_id=patient_id)
    print (payment)
    if payment:
        return jsonify({"payment_history": [ea_payment.json() for ea_payment in payment]})
    else:
        return jsonify({"message": "No transaction history found"}), 404


@app.route('/')
def index():
    return render_template('index.php', pub_key = pub_key)

@app.route('/success/<string:session_id>')
def success(session_id):

    # Check if payment is confirmed
    events = stripe.Event.list(
    type='checkout.session.completed',
    created={
        # Check for events created in the last hour.
        'gte': int(time.time() - 60 * 60 * 2),
    },
    )

    for event in events.auto_paging_iter():
        session = event['data']['object'] 
        session = dict(session) # convert stripe session class to dictionary
        if session['id'] == session_id:
            appointment_info = session['metadata']
            amount = session['display_items'][0]['amount']

    # add_to_transaction_history
    result = add_to_transaction_history(amount, appointment_info)
    if ( result == 'error'):
        return jsonify({"message": "An error occured creating payment"}), 500
    else:
        # add payment id to appointment info
        appointment_info['payment_id'] = result
        print (appointment_info)
    
    try:
        response = add_appointment(appointment_info)
        
        #Upon successful payment and appointment creation, notify the payment!
        for_patient_message = "An amount of $" + str(amount/100) + " has been successfully charged to your bank account for your appointment on " + str(appointment_info["date"]) + " at " + str(appointment_info["time"])
        result = {"patient_id": appointment_info['patient_id'], "message": for_patient_message}
        message = json.dumps(result, default=str)
        print ("book", message)
        channel.queue_declare(queue='notification', durable=True) # make sure the queue used by Shipping exist and durable
        channel.queue_bind(exchange=exchangename, queue='notification', routing_key='paymentSuccess.message') # make sure the queue is bound to the exchange    
        channel.basic_publish(exchange=exchangename, routing_key="paymentSuccess.message", body=message,
            properties=pika.BasicProperties(delivery_mode = 2))# make message persistent within the matching queues until it is received by some receiver (the matching queues have to exist and be durable and bound to the exchange, which are ensured by the previous two api calls)
        return jsonify({"appointment": appointment_info}), 200
    except Exception as e:
        return jsonify({"appointment": appointment_info}), 200

def add_to_transaction_history(amount, appointment_info):
    data = {
            'patient_id': appointment_info['patient_id'],
            'amount': float(amount)
        }

    payment = Payment(**data)
    payment.print_q()

    try:
        db.session.add(payment)
        db.session.commit()
        return payment.payment_id

    except SQLAlchemyError as e:
        error = str(e.__dict__['orig'])
        print (error)
        return 'error'


# This function generate a line item for stripe checkout to the appropriate format
# Compulsory parameters: amount, currency, name, quantity
# Full details here:
# https://stripe.com/docs/api/checkout/sessions/create#create_checkout_session-line_items
def generate_line_item(doctor_id, time, date, price):
    name = "Appointment Booking"
    amount = price
    currency = "sgd"
    quantity = 1
    description = time + " on " + date

    line_items = [{
        "name": name,
        "amount": amount,
        "currency": "sgd",
        "quantity": quantity,
        "description": description
    }]
    
    return line_items


@app.route('/checkout', methods=['POST'])
def checkout():
    # check data posted
    if request.is_json:
        try:
            data = request.get_json()

            patient_id = data['patient_id']
            doctor_id = data['doctor_id']
            time = data['time']
            date = data['date']
            price = int(data['price']) * 100 # Stripe calculate in cents

            # create metadata for later use
            appointment_info = {
                "patient_id": patient_id,
                "doctor_id": doctor_id,
                "time": time,
                "date": date
            }

            line_items = generate_line_item(doctor_id, time, date, price)

            session = stripe.checkout.Session.create(
                payment_method_types=['card'],
                line_items=line_items,
                success_url = 'http://localhost:80/ESD-ClinicAppointmentServices/app/ui/patient/patientUpdateAppts.php?session_id={CHECKOUT_SESSION_ID}',
                cancel_url='http://localhost:80/ESD-ClinicAppointmentServices/app/ui/patient/patientViewAllDoctors.php',
                metadata = appointment_info
            )

            checkout_session_id = session.id
            
            return jsonify({'CHECKOUT_SESSION_ID': checkout_session_id, 'pub_key':pub_key  }), 200 

        except KeyError:
            return jsonify({"message": "Invalid data parsed. Check required data needed."}), 400
        except stripe.error.InvalidRequestError:
            return jsonify({"message": "Invalid data parsed. Ensure data follow requirements set by Stripe."}), 400
    else:
        data = request.get_data()
        print (type(data))
        replymessage = json.dumps({"message": "Data should be in JSON", "data": data}, default=str)
        return replymessage, 400 

def add_appointment(appointment_info):
    try:
        url = "http://" + appointment_ip + "/create-appointment"
        response = requests.post(url, json=appointment_info)
        json_response = response.json()
        return json_response
    except HTTPError as http_err:
        return 'HTTP error occurred: {http_err}'  
    except Exception as err:
        return 'Other error occurred: {err}' 

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5005, debug = True)
