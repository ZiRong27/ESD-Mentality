# publish.py
import pika, os

# Access the CLODUAMQP_URL environment variable and parse it (fallback to localhost)
url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
params = pika.URLParameters(url)
connection = pika.BlockingConnection(params)
channel = connection.channel() # start a channel
channel.queue_declare(queue='hello') # Declare a queue
channel.basic_publish(exchange='',
                      routing_key='hello',
                      body='Hello CloudAMQP! By SQ')

print(" [x] Sent 'Hello World!'")
connection.close()