from flask import Flask, render_template, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS

# pip install --upgrade stripe
import stripe

from os import environ


app = Flask(__name__)

# To save key into os environment variable
pub_key = environ.get('pub_key')
secret_key = environ.get('secret_key')

stripe.api_key = secret_key

@app.route('/')
def index():
    return render_template('index.php', pub_key = pub_key)

@app.route('/success')
def success():
    print (request.get_json())
    return render_template('success.html', pub_key = pub_key)


# def payment_intent():
#     intent = stripe.PaymentIntent.create(
#         amount=1099,
#         currency='sgd',
#         payment_method_types=["card"],
#         # Verify your integration in this guide by including this parameter
#         metadata={'integration_check': 'accept_a_payment'},)

# Expected JSON for POST
# 'line_items' [{'name', 'amount', 'amount', 'currency'}]

@app.route('/checkout', methods=['POST'])
def checkout():
    # check data posted
    try:
        data = request.get_json()  
        patient_id = data['patient_id']
        line_items = data['line_items']

        session = stripe.checkout.Session.create(
            payment_method_types=['card'],
            line_items=line_items,
            success_url='http://127.0.0.1:5000/success?session_id={CHECKOUT_SESSION_ID}?patient_id={patient_id}',
            cancel_url='https://example.com/cancel',
        )

        checkout_session_id = session.id
        return render_template('checkout.html', pub_key = pub_key,CHECKOUT_SESSION_ID = checkout_session_id, patient_id=patient_id)
    except KeyError:
        return jsonify({"message": "Invalid data parsed. Check required data needed."}), 400
    except stripe.error.InvalidRequestError:
        return jsonify({"message": "Invalid data parsed. Ensure data follow requirements set by Stripe."}), 400
  

if __name__ == '__main__':
    app.run(debug=True, port=5005)