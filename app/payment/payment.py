from flask import Flask, render_template, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS

# pip install --upgrade stripe
import stripe

import time
import json
import datetime

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root:root@localhost:8889/esd_payment'
# app.config['SQLALCHEMY_DATABASE_URI'] = environ.get('dbURL')
# app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://admin:IloveESMandPaul!<3@esd.cemjatk2jkn2.ap-southeast-1.rds.amazonaws.com/esd_patient'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
 
db = SQLAlchemy(app)
CORS(app)

class Payment(db.Model):
    __tablename__ = 'payment'
    patient_id = db.Column(db.String, nullable=False)
    date = db.Column(db.Date, nullable = False)
    payment_id = db.Column(db.Integer, primary_key=True)
    amount = db.Column(db.Float, nullable=False)

    # def __init__(self, patient_id, amount):
    #     # sets the properties (of itself when created)
    #     self.patient_id = patient_id
    #     self.amount = amount
  

    def json(self):
        dto = {
            'patient_id': self.patient_id, 
            'date' : self.date ,
            'payment_id' : self.payment_id ,
            'amount' : self.amount
        }
        return dto  


pub_key = 'pk_test_RTVKG6eUSKaY6R0IJvaK2Yp900zQwhahx5'
stripe.api_key = 'sk_test_Jv4rWn5PYDRId7XNFssqQTS600rcH2uAbV'

@app.route('/')
def index():
    return render_template('index.php', pub_key = pub_key)

@app.route('/success/<string:session_id>')
def success(session_id):

    session_id = session_id.split('=')[1]

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

    # add_to_transaction_history(amount, appointment_info)
    # add_appointment()

    # add_to_transaction_history
    data = {
        'patient_id': appointment_info['patient_id'],
        'amount': float(amount)
    }

    payment = Payment(**data)

    try:
        print(db.session.add(payment))
        print(db.session.commit())

    except:
        return jsonify({"message": "An error occured creating payment"}), 500

    return render_template('success.html', pub_key = pub_key)

# def add_to_transaction_history(amount, appointment_info):

#     data = {
#         'patient_id': appointment_info['patient_id'],
#         'amount': float(amount)
#     }

#     payment = Payment(**data)

#     try:
#         db.session.add(payment)
#         db.session.commit()

#     except:
#         return jsonify({"message": "An error occured creating payment"}), 500


# This function generate a line item for checkout to the appropriate format
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
        data = request.get_json()  
        print (data)

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
        print(line_items)

        session = stripe.checkout.Session.create(
            payment_method_types=['card'],
            line_items=line_items,
            success_url='http://127.0.0.1:5005/success/session_id={CHECKOUT_SESSION_ID}',
            cancel_url='https://example.com/cancel',
            metadata = appointment_info
        )

        checkout_session_id = session.id
        return render_template('checkout.html', pub_key = pub_key,CHECKOUT_SESSION_ID = checkout_session_id, appointment_info = doctor_id)
    # except KeyError:
    #     return jsonify({"message": "Invalid data parsed. Check required data needed."}), 400
    # except stripe.error.InvalidRequestError:
    #     return jsonify({"message": "Invalid data parsed. Ensure data follow requirements set by Stripe."}), 400
    else:
        data = request.get_data()
        print (type(data))

        replymessage = json.dumps({"message": "Data should be in JSON", "data": data}, default=str)
        return replymessage, 400 
  

if __name__ == '__main__':
    app.run(debug=True, port=5005)