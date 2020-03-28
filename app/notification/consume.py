# consume.py
import pika, os

# Access the CLODUAMQP_URL environment variable and parse it (fallback to localhost)
url = 'amqp://xhnawuvi:znFCiYKqjzNmdGBNLdzTJ07R25lNOCr_@vulture.rmq.cloudamqp.com/xhnawuvi'
params = pika.URLParameters(url)
connection = pika.BlockingConnection(params)
channel = connection.channel() # start a channel
# set up the exchange if the exchange doesn't exist
exchangename="appointment_topic"
channel.exchange_declare(exchange=exchangename, exchange_type='topic')
channelqueue = channel.queue_declare(queue="notification", durable=True) # 'durable' makes the queue survive broker restarts so that the messages in it survive broker restarts too
queue_name = channelqueue.method.queue
channel.queue_bind(exchange=exchangename, queue=queue_name, routing_key='*.message') # bind the queue to the exchange via the key

def callback(ch, method, properties, body):
  print(" [x] Received " + str(body))

channel.basic_consume('notification',
                      callback,
                      auto_ack=True)

print(' [*] Waiting for messages:')
channel.start_consuming()
connection.close()