# publish.py
import pika, os

# Access the CLODUAMQP_URL environment variable and parse it (fallback to localhost)
url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
params = pika.URLParameters(url)
connection = pika.BlockingConnection(params)
channel = connection.channel() # start a channel
exchangename="appointment_topic"
channel.exchange_declare(exchange=exchangename, exchange_type='topic')
channel.basic_publish(exchange=exchangename,
                      routing_key='paymentSuccess.message',
                      body='Hello CloudAMQP! By SQ',             
                      properties=pika.BasicProperties(delivery_mode = 2)# make message persistent within the matching queues until it is received by some receiver (the matching queues have to exist and be durable and bound to the exchange, which are ensured by the previous two api calls)
)

print(" [x] Sent 'Hello World!'")
connection.close()